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
            throw new InvalidArgumentException('Incompatible salary-increase input.');
        }

        $percentageIncrease = $input->currentSalary->percentage($input->adjustmentRate);
        $monthlyIncrease = $percentageIncrease->add($input->fixedAddition);
        $newSalary = $input->currentSalary->add($monthlyIncrease);
        $retroactive = $monthlyIncrease->multiply($input->retroactiveMonths);
        $annualPayrollImpact = $monthlyIncrease->multiply(12);
        $effectiveBasisPoints = intdiv($monthlyIncrease->minorAmount() * 10000, $input->currentSalary->minorAmount());
        $effectiveAdjustment = number_format($effectiveBasisPoints / 100, 2, '.', ',').'%';

        $memory = new CalculationMemory(
            schemaVersion: self::RULE_VERSION,
            steps: [
                new CalculationMemoryStep('percentage_increase', 'Percentage increase', 'current pay × increase percentage', ['current_salary' => $input->currentSalary->minorAmount(), 'adjustment_rate' => $input->adjustmentRate->toDecimalString()], $percentageIncrease->minorAmount(), 'Calculated in cents using Money::percentage.'),
                new CalculationMemoryStep('monthly_increase', 'Monthly increase', 'percentage increase + fixed increase', ['percentage_increase' => $percentageIncrease->minorAmount(), 'fixed_addition' => $input->fixedAddition->minorAmount()], $monthlyIncrease->minorAmount(), 'Monetary values are calculated in cents.'),
                new CalculationMemoryStep('new_salary', 'New monthly pay', 'current pay + monthly increase', ['current_salary' => $input->currentSalary->minorAmount(), 'monthly_increase' => $monthlyIncrease->minorAmount()], $newSalary->minorAmount(), 'Monetary values are calculated in cents.'),
                new CalculationMemoryStep('retroactive', 'Retroactive difference', 'monthly increase × retroactive months', ['monthly_increase' => $monthlyIncrease->minorAmount(), 'months' => $input->retroactiveMonths], $retroactive->minorAmount(), 'Monetary values are calculated in cents.'),
                new CalculationMemoryStep('effective_adjustment', 'Effective increase', 'monthly increase ÷ current pay', ['monthly_increase' => $monthlyIncrease->minorAmount(), 'current_salary' => $input->currentSalary->minorAmount()], $effectiveAdjustment, 'Displayed as a percentage with two decimals.'),
                new CalculationMemoryStep('annual_impact', 'Annual pay impact', 'monthly increase × 12', ['monthly_increase' => $monthlyIncrease->minorAmount()], $annualPayrollImpact->minorAmount(), 'Twelve-month projection.'),
            ],
            assumptions: ['The annual impact is a 12-month pay projection. Employer payroll taxes, benefits, bonuses, and other compensation are not included.'],
            isEstimate: true,
        );

        return new ToolCalculationResult(
            'salary-increase-calculator',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('new_salary', 'New monthly pay', $newSalary->formatEnUs()),
                new ToolCalculationSummaryItem('monthly_increase', 'Monthly increase', $monthlyIncrease->formatEnUs()),
                new ToolCalculationSummaryItem('effective_adjustment', 'Effective increase', $effectiveAdjustment, 'Effective percentage including both percentage and fixed increases.'),
                new ToolCalculationSummaryItem('retroactive_difference', 'Retroactive difference', $retroactive->formatEnUs(), $input->retroactiveMonths.' month(s) entered.'),
                new ToolCalculationSummaryItem('annual_payroll_impact', 'Annual pay impact', $annualPayrollImpact->formatEnUs(), 'Twelve-month pay projection; employer payroll costs are excluded.'),
            ],
            ['input' => $input->toArray()],
            calculationMemory: $memory,
        );
    }
}
