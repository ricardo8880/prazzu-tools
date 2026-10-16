<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Domain\Services;

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
use App\Tools\ProfitDistributionBalanceSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible input.');
        }

        $retained = Money::fromDecimal($input->retainedEarnings);
        $netIncome = Money::fromDecimal($input->currentYearNetIncome);
        $cash = Money::fromDecimal($input->cashAvailable);
        $reserve = Money::fromDecimal($input->minimumCashReserve);
        $prior = Money::fromDecimal($input->priorDistributions);
        $planned = Money::fromDecimal($input->plannedDistribution);
        $monthlyIncome = Money::fromDecimal($input->monthlyNetIncome);
        $growth = Percentage::fromString($input->monthlyGrowthRate);

        $equityCapacity = max(0, $retained->minorAmount() + $netIncome->minorAmount() - $prior->minorAmount());
        $cashCapacity = max(0, $cash->minorAmount() - $reserve->minorAmount());
        $recommendedCeiling = min($equityCapacity, $cashCapacity);
        $plannedMinor = $planned->minorAmount();
        $remainingCash = max(0, $cash->minorAmount() - $plannedMinor);
        $withinCeiling = $plannedMinor <= $recommendedCeiling;

        $rows = [];
        $projectedEquity = $equityCapacity;
        $projectedCash = $cashCapacity;
        $currentIncome = $monthlyIncome->minorAmount();
        for ($month = 1; $month <= $input->planningMonths; $month++) {
            if ($month > 1) {
                $currentIncome += Money::fromMinor($currentIncome)->percentage($growth)->minorAmount();
            }
            $projectedEquity += $currentIncome;
            $projectedCash += $currentIncome;
            $rows[] = [
                'month' => $month,
                'projected_net_income_minor' => $currentIncome,
                'projected_equity_capacity_minor' => $projectedEquity,
                'projected_cash_capacity_minor' => $projectedCash,
                'projected_distribution_ceiling_minor' => min($projectedEquity, $projectedCash),
            ];
        }

        return new ToolCalculationResult(
            toolSlug: 'owner-distribution-planner',
            schemaVersion: '2.0.0',
            summary: [
                new ToolCalculationSummaryItem('equity_capacity', 'Equity-based capacity', Money::fromMinor($equityCapacity)->formatEnUs()),
                new ToolCalculationSummaryItem('cash_capacity', 'Cash-based capacity', Money::fromMinor($cashCapacity)->formatEnUs()),
                new ToolCalculationSummaryItem('ceiling', 'Conservative planning ceiling', Money::fromMinor($recommendedCeiling)->formatEnUs()),
                new ToolCalculationSummaryItem('planned', 'Planned distribution', $planned->formatEnUs(), $withinCeiling ? 'Within the planning ceiling' : 'Above the planning ceiling'),
            ],
            details: [
                'input' => $input->toArray(),
                'equity_capacity_minor' => $equityCapacity,
                'cash_capacity_minor' => $cashCapacity,
                'recommended_ceiling_minor' => $recommendedCeiling,
                'planned_distribution_minor' => $plannedMinor,
                'remaining_cash_minor' => $remainingCash,
                'within_ceiling' => $withinCeiling,
                'planning' => ['rows' => $rows],
            ],
            warnings: [
                new ToolCalculationWarning('entity_tax', 'Owner distributions are taxed differently depending on entity type (for example, partnership, S corporation, or C corporation). This planner does not determine tax treatment.', ToolCalculationWarningLevel::Info),
                new ToolCalculationWarning('legal_accounting', 'A positive planning capacity does not prove a distribution is legally permitted. Confirm governing documents, state law, solvency requirements, lender covenants, and accounting records.', ToolCalculationWarningLevel::Info),
            ],
            calculationMemory: new CalculationMemory('2.0.0', [
                new CalculationMemoryStep('equity_capacity', 'Equity capacity', 'retained earnings + current-year net income - prior distributions', ['retained_earnings_minor' => $retained->minorAmount(), 'net_income_minor' => $netIncome->minorAmount(), 'prior_distributions_minor' => $prior->minorAmount()], $equityCapacity),
                new CalculationMemoryStep('cash_capacity', 'Cash capacity', 'cash available - minimum cash reserve', ['cash_minor' => $cash->minorAmount(), 'reserve_minor' => $reserve->minorAmount()], $cashCapacity),
                new CalculationMemoryStep('ceiling', 'Conservative ceiling', 'minimum of equity capacity and cash capacity', ['equity_capacity_minor' => $equityCapacity, 'cash_capacity_minor' => $cashCapacity], $recommendedCeiling),
            ], assumptions: ['The figures are management estimates supplied by the user.', 'No entity-level or owner-level tax is calculated.'], isEstimate: true),
        );
    }
}
