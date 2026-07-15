<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Data;

final readonly class FeeAdjustmentResult
{
    public function __construct(
        public int $currentValueCents,
        public float $percentage,
        public int $differenceCents,
        public int $adjustedValueCents,
    ) {}

    public function toArray(): array
    {
        return [
            'current_value_cents' => $this->currentValueCents,
            'percentage' => $this->percentage,
            'difference_cents' => $this->differenceCents,
            'adjusted_value_cents' => $this->adjustedValueCents,
        ];
    }
}
