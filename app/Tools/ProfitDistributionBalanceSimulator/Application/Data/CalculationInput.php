<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $annualRevenue,
        public string $accountingProfit,
        public string $referenceMargin,
        public string $taxesOnRevenue,
        public string $priorDistributions = '0',
        public string $monthlyProLabore = '0',
        public string $monthlyGrowthRate = '0',
        public int $planningMonths = 12,
        public bool $simulateBookkeeping = false,
        public string $operatingExpenses = '0',
        public string $otherExpenses = '0',
    ) {}

    public function toArray(): array
    {
        return [
            'annual_revenue' => $this->annualRevenue,
            'accounting_profit' => $this->accountingProfit,
            'reference_margin' => $this->referenceMargin,
            'taxes_on_revenue' => $this->taxesOnRevenue,
            'prior_distributions' => $this->priorDistributions,
            'monthly_pro_labore' => $this->monthlyProLabore,
            'monthly_growth_rate' => $this->monthlyGrowthRate,
            'planning_months' => $this->planningMonths,
            'simulate_bookkeeping' => $this->simulateBookkeeping,
            'operating_expenses' => $this->operatingExpenses,
            'other_expenses' => $this->otherExpenses,
        ];
    }
}
