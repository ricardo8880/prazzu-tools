<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Data;

use App\Core\Money\Money;

final readonly class PresumedProfitInput
{
    /** @param array<string, Money> $activityRevenue */
    public function __construct(
        public int $quarter,
        public array $activityRevenue,
        public Money $otherTaxableAdditions,
        public Money $priorIrpjPresumptionRevenue,
        public Money $priorCsllPresumptionRevenue,
        public Money $irpjCredits,
        public Money $csllCredits,
    ) {}
}
