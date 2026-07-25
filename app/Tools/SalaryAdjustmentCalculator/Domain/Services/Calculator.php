<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\SalaryAdjustmentCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
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

        return new ToolCalculationResult(
            'reajuste-salarial',
            '1.0.0',
            [
                new ToolCalculationSummaryItem('new_salary', 'Novo salário', $newSalary->formatPtBr()),
                new ToolCalculationSummaryItem('monthly_increase', 'Diferença mensal', $monthlyIncrease->formatPtBr()),
                new ToolCalculationSummaryItem('retroactive_difference', 'Diferença retroativa', $retroactive->formatPtBr(), $input->retroactiveMonths.' mês(es) informado(s).'),
                new ToolCalculationSummaryItem('annual_payroll_impact', 'Impacto anual da remuneração', $annualPayrollImpact->formatPtBr(), '12 salários, 13º e adicional constitucional de férias.'),
            ],
            [
                'input' => $input->toArray(),
                'memory' => [
                    'Parcela percentual = salário atual × reajuste' => $percentageIncrease->formatPtBr(),
                    'Diferença mensal = parcela percentual + aumento fixo' => $monthlyIncrease->formatPtBr(),
                    'Novo salário = salário atual + diferença mensal' => $newSalary->formatPtBr(),
                    'Retroativo = diferença mensal × meses' => $retroactive->formatPtBr(),
                    'Impacto anual = diferença mensal × (12 + 1 + 1/3)' => $annualPayrollImpact->formatPtBr(),
                ],
            ],
        );
    }
}
