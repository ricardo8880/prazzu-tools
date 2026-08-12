<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $currentAnnualRevenue,
        public string $projectedAnnualRevenue,
        public string $meEffectiveTaxRate = '6',
        public string $monthlyAccountingCost = '0',
        public string $monthlyOtherCost = '0',
        public string $monthlyMeiCost = '0',
        public string $annualGrowthRate = '0',
        public int $projectionYears = 3,
        public string $targetFixedCostBurden = '10',
    ) {}

    public function toArray(): array
    {
        return [
            'current_annual_revenue' => $this->currentAnnualRevenue,
            'projected_annual_revenue' => $this->projectedAnnualRevenue,
            'me_effective_tax_rate' => $this->meEffectiveTaxRate,
            'monthly_accounting_cost' => $this->monthlyAccountingCost,
            'monthly_other_cost' => $this->monthlyOtherCost,
            'monthly_mei_cost' => $this->monthlyMeiCost,
            'annual_growth_rate' => $this->annualGrowthRate,
            'projection_years' => $this->projectionYears,
            'target_fixed_cost_burden' => $this->targetFixedCostBurden,
        ];
    }
}
