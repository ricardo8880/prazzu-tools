<?php

declare(strict_types=1);

namespace App\Tools\IssCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\IssCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a Calculadora de ISS.');
        }
        if ($input->services === []) {
            throw new InvalidArgumentException('Informe ao menos um serviço.');
        }
        $rows = [];
        $totalValue = 0;
        $totalIss = 0;
        $totalRetained = 0;
        $municipal = [];
        $monthly = [];
        foreach ($input->services as $service) {
            $value = Money::fromDecimal((string) $service['value']);
            $rate = Percentage::fromString((string) $service['rate']);
            if ($value->minorAmount() <= 0 || $rate->millionthsOfPercent() < 0) {
                throw new InvalidArgumentException('Valor e alíquota devem ser válidos.');
            }
            $iss = $value->percentage($rate)->minorAmount();
            $retained = (bool) ($service['retained'] ?? false);
            $row = ['competence' => (string) ($service['competence'] ?? ''), 'municipality' => (string) $service['municipality'], 'service' => (string) $service['service'], 'taker' => (string) ($service['taker'] ?? ''), 'value_minor' => $value->minorAmount(), 'rate' => $rate->toDecimalString(), 'iss_minor' => $iss, 'retained' => $retained, 'net_receivable_minor' => $retained ? max(0, $value->minorAmount() - $iss) : $value->minorAmount()];
            $rows[] = $row;
            $totalValue += $value->minorAmount();
            $totalIss += $iss;
            if ($retained) {
                $totalRetained += $iss;
            }
            $key = $row['municipality'];
            if (! isset($municipal[$key])) {
                $municipal[$key] = ['municipality' => $key, 'services' => 0, 'value_minor' => 0, 'iss_minor' => 0];
            } $municipal[$key]['services']++;
            $municipal[$key]['value_minor'] += $value->minorAmount();
            $municipal[$key]['iss_minor'] += $iss;
            $monthKey = $row['competence'].'|'.$row['municipality'];
            if (! isset($monthly[$monthKey])) {
                $monthly[$monthKey] = ['competence' => $row['competence'], 'municipality' => $row['municipality'], 'services' => 0, 'value_minor' => 0, 'iss_minor' => 0, 'retained_minor' => 0, 'payable_by_provider_minor' => 0];
            } $monthly[$monthKey]['services']++;
            $monthly[$monthKey]['value_minor'] += $value->minorAmount();
            $monthly[$monthKey]['iss_minor'] += $iss;
            if ($retained) {
                $monthly[$monthKey]['retained_minor'] += $iss;
            } else {
                $monthly[$monthKey]['payable_by_provider_minor'] += $iss;
            }
        }
        $main = $rows[0];
        $scenarios = [];
        foreach ($input->municipalityScenarios as $scenario) {
            $rate = Percentage::fromString((string) $scenario['rate']);
            $iss = Money::fromMinor($main['value_minor'])->percentage($rate)->minorAmount();
            $scenarios[] = ['municipality' => (string) $scenario['municipality'], 'rate' => $rate->toDecimalString(), 'iss_minor' => $iss, 'difference_minor' => $iss - $main['iss_minor']];
        }
        $warnings = [new ToolCalculationWarning('parametric', 'A ferramenta usa a alíquota informada e não identifica automaticamente a legislação, o código do serviço, o local de incidência ou hipóteses de retenção do município.', ToolCalculationWarningLevel::Info)];
        $mainRate = Percentage::fromString($main['rate'])->millionthsOfPercent();
        if ($mainRate < 2_000_000 || $mainRate > 5_000_000) {
            $warnings[] = new ToolCalculationWarning('rate_check', 'A alíquota informada está fora da faixa geral de 2% a 5% prevista nas normas nacionais do ISS. Confirme exceções e legislação municipal antes de usar o resultado.', ToolCalculationWarningLevel::Info);
        }

        return new ToolCalculationResult(toolSlug: 'calculadora-iss', schemaVersion: '1.0.0', summary: [
            new ToolCalculationSummaryItem('service_value', 'Valor do serviço principal', Money::fromMinor($main['value_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('rate', 'Alíquota informada', $main['rate'].'%'),
            new ToolCalculationSummaryItem('iss', 'ISS estimado', Money::fromMinor($main['iss_minor'])->formatPtBr()),
            new ToolCalculationSummaryItem('net', 'Líquido após retenção', Money::fromMinor($main['net_receivable_minor'])->formatPtBr()),
        ], details: ['services' => $rows, 'totals' => ['value_minor' => $totalValue, 'iss_minor' => $totalIss, 'retained_minor' => $totalRetained, 'payable_by_provider_minor' => $totalIss - $totalRetained], 'municipal_consolidation' => array_values($municipal), 'monthly_consolidation' => array_values($monthly), 'municipality_scenarios' => $scenarios], warnings: $warnings, calculationMemory: new CalculationMemory('1.0.0', [
            new CalculationMemoryStep('iss', 'ISS estimado', 'valor do serviço × alíquota municipal informada', ['value_minor' => $main['value_minor'], 'rate' => $main['rate']], $main['iss_minor'], 'Arredondamento monetário em centavos.'),
            new CalculationMemoryStep('net', 'Líquido quando há retenção', 'valor do serviço − ISS retido', ['retained' => $main['retained']], $main['net_receivable_minor']),
        ], assumptions: ['A alíquota e a responsabilidade por retenção são informadas pelo usuário.', 'Não há inferência automática de município competente, código de serviço, benefício, dedução de base ou regime especial.'], isEstimate: true));
    }
}
