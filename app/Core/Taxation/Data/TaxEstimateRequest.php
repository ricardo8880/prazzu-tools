<?php

declare(strict_types=1);

namespace App\Core\Taxation\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use DateTimeImmutable;

final readonly class TaxEstimateRequest
{
    public function __construct(
        public DateTimeImmutable $referenceDate,
        public string $activity,
        public Money $monthlyRevenue,
        public Money $revenueLastTwelveMonths,
        public Money $payrollLastTwelveMonths,
        public ?Percentage $indirectTaxRate = null,
        public ?Money $monthlyOperatingCosts = null,
        public ?Money $monthlyDeductibleExpenses = null,
        public ?Money $monthlyPisCofinsCreditBase = null,
    ) {}
}
