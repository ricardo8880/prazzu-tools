<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

final readonly class ProfitDistributionInput
{
    /** @param list<PartnerProfitShare> $partners */
    public function __construct(
        public Money $accountingProfit,
        public Money $accumulatedLosses,
        public Money $reservesAndUnavailableAmounts,
        public Money $adjustments,
        public Money $priorDistributions,
        public ProfitDistributionCriterion $criterion,
        public array $partners,
        public ?Money $intendedDistribution = null,
    ) {}
}
