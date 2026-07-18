<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class FeeAdjustmentResult
{
    public function __construct(
        public Money $currentValue,
        public Percentage $percentage,
        public Money $difference,
        public Money $adjustedValue,
    ) {}

    /** @return array{current_value_cents: int, percentage: string, difference_cents: int, adjusted_value_cents: int} */
    public function toArray(): array
    {
        return [
            'current_value_cents' => $this->currentValue->minorAmount(),
            'percentage' => $this->percentage->toDecimalString(),
            'difference_cents' => $this->difference->minorAmount(),
            'adjusted_value_cents' => $this->adjustedValue->minorAmount(),
        ];
    }
}
