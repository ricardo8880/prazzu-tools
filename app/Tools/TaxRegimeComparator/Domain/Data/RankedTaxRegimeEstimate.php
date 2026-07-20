<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Data;

use App\Core\Money\Money;

final readonly class RankedTaxRegimeEstimate
{
    public function __construct(
        public int $position,
        public TaxRegimeEstimate $estimate,
        public Money $monthlyDifferenceFromLowest,
        public Money $annualDifferenceFromLowest,
    ) {}
}
