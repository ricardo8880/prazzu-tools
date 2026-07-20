<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Validators\TaxComparisonScenarioValidator;
use DateTimeImmutable;

final readonly class TaxComparisonScenario
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
    ) {
        (new TaxComparisonScenarioValidator)->validate($this);
    }

    public function annualRevenueProjection(): Money
    {
        return $this->monthlyRevenue->multiply(12);
    }

    public function annualOperatingCostsProjection(): Money
    {
        return $this->monthlyOperatingCosts->multiply(12);
    }

    public function annualDeductibleExpensesProjection(): Money
    {
        return $this->monthlyDeductibleExpenses->multiply(12);
    }
}
