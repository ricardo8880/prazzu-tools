<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class PartnerProfitShare
{
    public function __construct(
        public string $key,
        public Percentage $ownershipPercentage,
        public ?Money $definedAmount = null,
        public ?string $label = null,
    ) {}
}
