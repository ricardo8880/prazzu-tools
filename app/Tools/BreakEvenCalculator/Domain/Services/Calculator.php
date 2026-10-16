<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\BreakEvenCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible break-even input.');
        }

        $price = $input->salePrice->minorAmount();
        $contribution = $price - $input->variableCost->minorAmount();
        if ($price <= 0 || $contribution <= 0) {
            throw new InvalidArgumentException('Selling price must be positive and greater than variable cost.');
        }

        $fixed = $input->fixedCosts->minorAmount();
        $units = intdiv($fixed + $contribution - 1, $contribution);
        if ($units > intdiv(PHP_INT_MAX, $price)) {
            throw new InvalidArgumentException('Calculated revenue exceeds the supported monetary range.');
        }

        $revenue = Money::fromMinor($units * $price);
        $marginBasisPoints = intdiv($contribution * 10000, $price);
        $unitContribution = Money::fromMinor($contribution);
        $coverageSurplus = Money::fromMinor(($units * $contribution) - $fixed);
        $formattedMargin = number_format($marginBasisPoints / 100, 2, ',', '.').' %';

        $warnings = [];
        if ($fixed === 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'no_fixed_costs',
                message: 'With zero fixed costs, accounting break-even occurs before the first unit. The calculator keeps minimum units and revenue at zero.',
                level: ToolCalculationWarningLevel::Info,
                title: 'No fixed costs entered',
            );
        }

        return new ToolCalculationResult(
            toolSlug: 'break-even-calculator',
            schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('break_even_revenue', 'Minimum break-even revenue', $revenue->formatEnUs(), 'Revenue at the first whole unit that covers fixed costs.'),
                new ToolCalculationSummaryItem('break_even_units', 'Minimum units', $units.' unidade'.($units === 1 ? '' : 's')),
                new ToolCalculationSummaryItem('unit_contribution', 'Unit contribution margin', $unitContribution->formatEnUs()),
                new ToolCalculationSummaryItem('contribution_margin', 'Contribution margin ratio', $formattedMargin),
                new ToolCalculationSummaryItem('coverage_surplus', 'Rounding surplus', $coverageSurplus->formatEnUs(), 'Excess contribution at the first whole unit that covers fixed costs.'),
            ],
            details: ['input' => $input->toArray()],
            warnings: $warnings,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep('unit_contribution', 'Unit contribution margin', 'selling price − unit variable cost', ['sale_price' => $price, 'variable_cost' => $input->variableCost->minorAmount()], $contribution),
                    new CalculationMemoryStep('contribution_margin', 'Contribution margin ratio', 'unit contribution ÷ selling price', ['unit_contribution' => $contribution, 'sale_price' => $price], $formattedMargin, 'Percentage represented in basis points to two decimals.'),
                    new CalculationMemoryStep('break_even_units', 'Minimum units', 'ceiling(fixed costs ÷ unit contribution)', ['fixed_costs' => $fixed, 'unit_contribution' => $contribution], $units, 'Rounded up to the next whole unit.'),
                    new CalculationMemoryStep('break_even_revenue', 'Minimum break-even revenue', 'minimum units × selling price', ['break_even_units' => $units, 'sale_price' => $price], $revenue->minorAmount(), 'Calculated in cents using integer arithmetic.'),
                    new CalculationMemoryStep('coverage_surplus', 'Rounding surplus', '(minimum units × unit contribution) − fixed costs', ['break_even_units' => $units, 'unit_contribution' => $contribution, 'fixed_costs' => $fixed], $coverageSurplus->minorAmount(), 'Shows the surplus created by rounding to a whole unit.'),
                ],
                assumptions: [
                    'Fixed costs, price, and variable cost should represent the same period and a comparable operating structure.',
                    'Unit selling price and unit variable cost are assumed constant across the units analyzed.',
                    'Taxes, commissions, and variable losses should be included in variable cost when applicable.',
                    'Minimum units are rounded up, so displayed revenue may exceed the theoretical fractional break-even point.',
                ],
                isEstimate: true,
            ),
        );
    }
}
