<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data;

use App\Core\Money\Money;

final readonly class ProfitDistributionResult
{
    /**
     * @param list<PartnerProfitDistribution> $partners
     * @param list<array<string, int|string|bool|null>> $memory
     * @param list<string> $warnings
     */
    public function __construct(
        public Money $accountingProfit,
        public Money $accumulatedLosses,
        public Money $reservesAndUnavailableAmounts,
        public Money $adjustments,
        public Money $priorDistributions,
        public Money $maximumAvailableProfit,
        public Money $distributedAmount,
        public Money $undistributedBalance,
        public array $partners,
        public array $memory,
        public array $warnings,
    ) {}
}
