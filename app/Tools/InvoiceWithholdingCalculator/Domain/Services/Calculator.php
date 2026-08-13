<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\InvoiceWithholdingCalculator\Application\Data\CalculationInput;
use App\Tools\InvoiceWithholdingCalculator\Domain\Rules\InvoiceWithholdingRule;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    private const TAXES = [
        'irrf' => ['label' => 'IRRF', 'apply' => 'applyIrrf', 'rate' => 'irrfRate', 'base' => 'irrfBasePercent'],
        'inss' => ['label' => 'INSS', 'apply' => 'applyInss', 'rate' => 'inssRate', 'base' => 'inssBasePercent'],
        'iss' => ['label' => 'ISS', 'apply' => 'applyIss', 'rate' => 'issRate', 'base' => 'issBasePercent'],
        'pis' => ['label' => 'PIS/Pasep', 'apply' => 'applyPis', 'rate' => 'pisRate', 'base' => 'pisBasePercent'],
        'cofins' => ['label' => 'Cofins', 'apply' => 'applyCofins', 'rate' => 'cofinsRate', 'base' => 'cofinsBasePercent'],
        'csll' => ['label' => 'CSLL', 'apply' => 'applyCsll', 'rate' => 'csllRate', 'base' => 'csllBasePercent'],
    ];

    public function __construct(private readonly InvoiceWithholdingRule $rule = new InvoiceWithholdingRule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a ferramenta calculadora-retencoes-nota-fiscal.');
        }
        $referenceDate = ReferenceDate::fromString($input->competence.'-01');
        if (! $this->rule->effectivePeriod()->contains($referenceDate)) {
            throw new InvalidArgumentException('Esta versão normativa atende competências de 2026.');
        }

        $notes = [['description' => $input->serviceDescription !== '' ? $input->serviceDescription : 'Nota principal', 'invoice_number' => $input->invoiceNumber, 'value' => Money::fromDecimal($input->grossValue)]];
        foreach ($input->notes as $note) {
            $value = Money::fromDecimal($note['value']);
            if ($value->minorAmount() <= 0) {
                continue;
            }
            $notes[] = ['description' => $note['description'] !== '' ? $note['description'] : 'Nota/serviço adicional', 'invoice_number' => '', 'value' => $value];
        }
        $gross = Money::zero();
        foreach ($notes as $note) {
            $gross = $gross->add($note['value']);
        }
        if ($gross->minorAmount() <= 0) {
            throw new InvalidArgumentException('O valor bruto total deve ser maior que zero.');
        }

        $taxes = [];
        $totalWithheld = Money::zero();
        foreach (self::TAXES as $key => $meta) {
            $enabled = $input->{$meta['apply']};
            $rate = Percentage::fromString($input->{$meta['rate']});
            $basePercent = Percentage::fromString($input->{$meta['base']});
            $base = $enabled ? $gross->percentage($basePercent) : Money::zero();
            $withheld = $enabled ? $base->percentage($rate) : Money::zero();
            $taxes[$key] = ['label' => $meta['label'], 'enabled' => $enabled, 'rate' => $rate->toDecimalString(), 'base_percent' => $basePercent->toDecimalString(), 'base_minor' => $base->minorAmount(), 'withheld_minor' => $withheld->minorAmount()];
            $totalWithheld = $totalWithheld->add($withheld);
        }
        if ($totalWithheld->minorAmount() > $gross->minorAmount()) {
            throw new InvalidArgumentException('Os parâmetros informados resultam em retenções superiores ao valor bruto. Revise alíquotas e bases.');
        }
        $net = $gross->subtract($totalWithheld);

        $noteBreakdown = [];
        foreach ($notes as $note) {
            $row = ['description' => $note['description'], 'invoice_number' => $note['invoice_number'], 'gross_minor' => $note['value']->minorAmount(), 'taxes' => [], 'withheld_minor' => 0];
            foreach (self::TAXES as $key => $meta) {
                $tax = $taxes[$key];
                $base = $tax['enabled'] ? $note['value']->percentage(Percentage::fromString($tax['base_percent'])) : Money::zero();
                $ret = $tax['enabled'] ? $base->percentage(Percentage::fromString($tax['rate'])) : Money::zero();
                $row['taxes'][$key] = ['base_minor' => $base->minorAmount(), 'withheld_minor' => $ret->minorAmount()];
                $row['withheld_minor'] += $ret->minorAmount();
            }
            $row['net_minor'] = $row['gross_minor'] - $row['withheld_minor'];
            $noteBreakdown[] = $row;
        }

        $steps = [new CalculationMemoryStep('gross', 'Valor bruto total', 'nota principal + notas/serviços adicionais', ['notes_count' => count($notes)], $gross->minorAmount())];
        foreach ($taxes as $key => $tax) {
            $steps[] = new CalculationMemoryStep($key, $tax['label'].' retido', 'valor bruto × percentual de base × alíquota', ['enabled' => $tax['enabled'], 'gross_minor' => $gross->minorAmount(), 'base_percent' => $tax['base_percent'], 'rate' => $tax['rate'], 'base_minor' => $tax['base_minor']], $tax['withheld_minor'], 'O cálculo só aplica o tributo quando ele está marcado como incidente.');
        }
        $steps[] = new CalculationMemoryStep('total', 'Total de retenções', 'IRRF + INSS + ISS + PIS/Pasep + Cofins + CSLL', ['gross_minor' => $gross->minorAmount()], $totalWithheld->minorAmount());
        $steps[] = new CalculationMemoryStep('net', 'Líquido estimado da nota', 'valor bruto − total de retenções', ['gross_minor' => $gross->minorAmount(), 'withheld_minor' => $totalWithheld->minorAmount()], $net->minorAmount());
        $memory = new CalculationMemory('1.0.0', $steps, [NormativeRuleSnapshot::fromRule($this->rule, $referenceDate)], [
            'A ferramenta é paramétrica: ela não decide automaticamente se cada retenção é aplicável ao serviço, prestador, tomador ou município.',
            'Bases percentuais abaixo de 100% devem ser usadas somente quando houver fundamento para exclusões, deduções ou base reduzida no caso concreto.',
            'ISS depende da legislação municipal e do local de incidência. INSS, IRRF e contribuições federais possuem hipóteses, dispensas e exceções próprias.',
            'Os valores servem para conferência e estimativa; valide natureza do serviço, enquadramento, limites de dispensa e legislação vigente antes do recolhimento.',
        ], true);

        return new ToolCalculationResult('calculadora-retencoes-nota-fiscal', '1.0.0', [
            new ToolCalculationSummaryItem('gross', 'Valor bruto', $gross->formatPtBr()),
            new ToolCalculationSummaryItem('withheld', 'Total estimado retido', $totalWithheld->formatPtBr()),
            new ToolCalculationSummaryItem('net', 'Líquido estimado', $net->formatPtBr()),
            new ToolCalculationSummaryItem('effective_rate', 'Retenção efetiva', $this->effectiveRate($totalWithheld->minorAmount(), $gross->minorAmount()).'%'),
        ], [
            'input' => $input->toArray(), 'gross_minor' => $gross->minorAmount(), 'total_withheld_minor' => $totalWithheld->minorAmount(), 'net_minor' => $net->minorAmount(), 'taxes' => $taxes, 'notes' => $noteBreakdown,
        ], [
            new ToolCalculationWarning('applicability', 'Marcar uma retenção significa que você confirmou sua aplicabilidade. A ferramenta não classifica automaticamente o serviço nem substitui a análise fiscal.', ToolCalculationWarningLevel::Info),
            new ToolCalculationWarning('municipal', 'Para ISS, confirme município competente, alíquota, responsabilidade pela retenção e eventuais regras locais.', ToolCalculationWarningLevel::Info),
        ], calculationMemory: $memory);
    }

    private function effectiveRate(int $withheldMinor, int $grossMinor): string
    {
        if ($grossMinor <= 0) {
            return '0,00';
        }
        $hundredths = intdiv(($withheldMinor * 10000) + intdiv($grossMinor,2),$grossMinor);

        return number_format($hundredths / 100,2,',','.');
    }
}
