<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\SalaryAdjustmentCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = '1.1.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $percentageIncrease = $input->currentSalary->percentage($input->adjustmentRate);
        $monthlyIncrease = $percentageIncrease->add($input->fixedAddition);
        $newSalary = $input->currentSalary->add($monthlyIncrease);
        $retroactive = $monthlyIncrease->multiply($input->retroactiveMonths);
        $annualPayrollImpact = $monthlyIncrease->multiply(40)->divide(3);
        $effectiveBasisPoints = intdiv($monthlyIncrease->minorAmount() * 10000, $input->currentSalary->minorAmount());
        $effectiveAdjustment = number_format($effectiveBasisPoints / 100, 2, ',', '.').' %';

        $memory = new CalculationMemory(
            schemaVersion: self::RULE_VERSION,
            steps: [
                new CalculationMemoryStep('percentage_increase', 'Parcela percentual', 'salário atual × percentual de reajuste', ['current_salary' => $input->currentSalary->minorAmount(), 'adjustment_rate' => $input->adjustmentRate->toDecimalString()], $percentageIncrease->minorAmount(), 'Money::percentage, em centavos.'),
                new CalculationMemoryStep('monthly_increase', 'Diferença mensal', 'parcela percentual + aumento fixo', ['percentage_increase' => $percentageIncrease->minorAmount(), 'fixed_addition' => $input->fixedAddition->minorAmount()], $monthlyIncrease->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('new_salary', 'Novo salário', 'salário atual + diferença mensal', ['current_salary' => $input->currentSalary->minorAmount(), 'monthly_increase' => $monthlyIncrease->minorAmount()], $newSalary->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('retroactive', 'Diferença retroativa', 'diferença mensal × meses retroativos', ['monthly_increase' => $monthlyIncrease->minorAmount(), 'months' => $input->retroactiveMonths], $retroactive->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('effective_adjustment', 'Reajuste efetivo', 'diferença mensal ÷ salário atual', ['monthly_increase' => $monthlyIncrease->minorAmount(), 'current_salary' => $input->currentSalary->minorAmount()], $effectiveAdjustment, 'Percentual truncado em basis points para duas casas decimais.'),
                new CalculationMemoryStep('annual_impact', 'Impacto anual da remuneração', 'diferença mensal × (12 + 1 + 1/3)', ['monthly_increase' => $monthlyIncrease->minorAmount()], $annualPayrollImpact->minorAmount(), 'Multiplicação por 40 e divisão por 3 com Money.'),
            ],
            assumptions: ['O impacto anual considera 12 salários, 13º salário e adicional constitucional de férias; encargos patronais não estão incluídos.'],
            isEstimate: true,
        );

        return new ToolCalculationResult(
            'reajuste-salarial',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('new_salary', 'Novo salário', $newSalary->formatPtBr()),
                new ToolCalculationSummaryItem('monthly_increase', 'Diferença mensal', $monthlyIncrease->formatPtBr()),
                new ToolCalculationSummaryItem('effective_adjustment', 'Reajuste efetivo', $effectiveAdjustment, 'Percentual efetivo considerando reajuste percentual e aumento fixo.'),
                new ToolCalculationSummaryItem('retroactive_difference', 'Diferença retroativa', $retroactive->formatPtBr(), $input->retroactiveMonths.' mês(es) informado(s).'),
                new ToolCalculationSummaryItem('annual_payroll_impact', 'Impacto anual da remuneração', $annualPayrollImpact->formatPtBr(), '12 salários, 13º e adicional constitucional de férias.'),
            ],
            ['input' => $input->toArray()],
            calculationMemory: $memory,
        );
    }
}
