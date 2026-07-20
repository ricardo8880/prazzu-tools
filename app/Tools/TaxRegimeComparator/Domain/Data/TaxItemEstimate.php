<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\TaxRegimeComparator\Domain\Validators\TaxItemEstimateValidator;

final readonly class TaxItemEstimate
{
    public function __construct(
        public string $code,
        public string $label,
        public Money $monthlyAmount,
        public Money $annualAmount,
        public ?Percentage $effectiveRate = null,
    ) {
        (new TaxItemEstimateValidator)->validate($this);
    }
}
