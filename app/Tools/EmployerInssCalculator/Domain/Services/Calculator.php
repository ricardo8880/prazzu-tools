<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmployerInssCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly EmployerChargeRule $chargeRule = new EmployerChargeRule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        $rates = $this->chargeRule->ratesFor($input->regime, $input->adjustedRat, $input->thirdParties);
        $cpp = $input->payroll->percentage($rates->cpp);
        $rat = $input->payroll->percentage($rates->rat);
        $third = $input->payroll->percentage($rates->thirdParties);
        $total = $cpp->add($rat)->add($third);
        $effective = $rates->cpp->millionthsOfPercent() + $rates->rat->millionthsOfPercent() + $rates->thirdParties->millionthsOfPercent();

        return new ToolCalculationResult('inss-patronal', '1.1.0', [
            new ToolCalculationSummaryItem('total', 'Total patronal estimado', $total->formatPtBr()),
            new ToolCalculationSummaryItem('cpp', 'CPP', $cpp->formatPtBr(), $rates->cpp->toDecimalString().'%'),
            new ToolCalculationSummaryItem('rat', 'RAT ajustado', $rat->formatPtBr(), $rates->rat->toDecimalString().'%'),
            new ToolCalculationSummaryItem('third_parties', 'Terceiros', $third->formatPtBr(), $rates->thirdParties->toDecimalString().'%'),
        ], ['input' => $input->toArray(), 'rates' => $rates->toArray(), 'effective_rate_millionths' => $effective, 'amounts' => ['cpp_minor' => $cpp->minorAmount(), 'rat_minor' => $rat->minorAmount(), 'third_parties_minor' => $third->minorAmount(), 'total_minor' => $total->minorAmount()]],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep('cpp', 'Contribuição patronal previdenciária', 'folha × CPP', ['payroll_minor' => $input->payroll->minorAmount(), 'cpp_rate' => $rates->cpp->toDecimalString()], $cpp->minorAmount(), 'Money::percentage'),
                new CalculationMemoryStep('rat', 'RAT ajustado', 'folha × RAT ajustado', ['payroll_minor' => $input->payroll->minorAmount(), 'rat_rate' => $rates->rat->toDecimalString()], $rat->minorAmount(), 'Money::percentage'),
                new CalculationMemoryStep('third_parties', 'Terceiros', 'folha × terceiros', ['payroll_minor' => $input->payroll->minorAmount(), 'third_parties_rate' => $rates->thirdParties->toDecimalString()], $third->minorAmount(), 'Money::percentage'),
                new CalculationMemoryStep('total', 'Total patronal', 'CPP + RAT + terceiros', ['cpp_minor' => $cpp->minorAmount(), 'rat_minor' => $rat->minorAmount(), 'third_parties_minor' => $third->minorAmount()], $total->minorAmount(), 'Money::add'),
            ], [$this->chargeRule->snapshot(ReferenceDate::fromString('2026-07-25'))], ['RAT ajustado e terceiros são parâmetros fornecidos pelo usuário.'], true));
    }
}
