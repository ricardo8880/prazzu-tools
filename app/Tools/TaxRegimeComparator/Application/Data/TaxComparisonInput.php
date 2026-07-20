<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use DateTimeImmutable;

final readonly class TaxComparisonInput implements ToolCalculationInput
{
    public function __construct(
        public DateTimeImmutable $referenceDate,
        public BusinessActivity $businessActivity,
        public Money $monthlyRevenue,
        public Money $revenueLastTwelveMonths,
        public Money $payrollLastTwelveMonths,
        public Money $monthlyOperatingCosts,
        public Money $monthlyDeductibleExpenses,
        public ?Money $monthlyPisCofinsCreditBase = null,
        public ?Percentage $indirectTaxRate = null,
        public ?string $state = null,
        public ?string $municipality = null,
    ) {}

    public function toScenario(): TaxComparisonScenario
    {
        return new TaxComparisonScenario(
            referenceDate: $this->referenceDate,
            businessActivity: $this->businessActivity,
            monthlyRevenue: $this->monthlyRevenue,
            revenueLastTwelveMonths: $this->revenueLastTwelveMonths,
            payrollLastTwelveMonths: $this->payrollLastTwelveMonths,
            monthlyOperatingCosts: $this->monthlyOperatingCosts,
            monthlyDeductibleExpenses: $this->monthlyDeductibleExpenses,
            monthlyPisCofinsCreditBase: $this->monthlyPisCofinsCreditBase,
            indirectTaxRate: $this->indirectTaxRate,
            state: $this->state,
            municipality: $this->municipality,
        );
    }

    /** @return array<string, int|string|null> */
    public function toArray(): array
    {
        return [
            'reference_date' => $this->referenceDate->format('Y-m-d'),
            'business_activity' => $this->businessActivity->value,
            'monthly_revenue_minor' => $this->monthlyRevenue->minorAmount(),
            'revenue_last_twelve_months_minor' => $this->revenueLastTwelveMonths->minorAmount(),
            'payroll_last_twelve_months_minor' => $this->payrollLastTwelveMonths->minorAmount(),
            'monthly_operating_costs_minor' => $this->monthlyOperatingCosts->minorAmount(),
            'monthly_deductible_expenses_minor' => $this->monthlyDeductibleExpenses->minorAmount(),
            'monthly_pis_cofins_credit_base_minor' => $this->monthlyPisCofinsCreditBase?->minorAmount(),
            'indirect_tax_rate' => $this->indirectTaxRate?->toDecimalString(),
            'state' => $this->state,
            'municipality' => $this->municipality,
        ];
    }
}
