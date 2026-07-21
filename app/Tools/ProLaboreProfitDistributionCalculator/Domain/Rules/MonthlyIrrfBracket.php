<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class MonthlyIrrfBracket
{
    public function __construct(
        public ?Money $upperLimit,
        public Percentage $rate,
        public Money $deduction,
    ) {}

    public function contains(Money $base): bool
    {
        return $this->upperLimit === null || $base->minorAmount() <= $this->upperLimit->minorAmount();
    }
}
