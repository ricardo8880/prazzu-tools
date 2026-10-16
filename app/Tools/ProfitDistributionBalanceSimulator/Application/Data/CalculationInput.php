<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $retainedEarnings,
        public string $currentYearNetIncome,
        public string $cashAvailable,
        public string $minimumCashReserve,
        public string $priorDistributions = '0',
        public string $plannedDistribution = '0',
        public string $monthlyNetIncome = '0',
        public string $monthlyGrowthRate = '0',
        public int $planningMonths = 12,
    ) {}

    public function toArray(): array
    {
        return [
            'retained_earnings' => $this->retainedEarnings,
            'current_year_net_income' => $this->currentYearNetIncome,
            'cash_available' => $this->cashAvailable,
            'minimum_cash_reserve' => $this->minimumCashReserve,
            'prior_distributions' => $this->priorDistributions,
            'planned_distribution' => $this->plannedDistribution,
            'monthly_net_income' => $this->monthlyNetIncome,
            'monthly_growth_rate' => $this->monthlyGrowthRate,
            'planning_months' => $this->planningMonths,
        ];
    }
}
