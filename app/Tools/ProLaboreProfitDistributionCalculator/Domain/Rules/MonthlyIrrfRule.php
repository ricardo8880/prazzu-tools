<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Money\Money;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeRuleMetadata;

final readonly class MonthlyIrrfRule implements EffectiveDated, NormativeRule
{
    /** @param list<MonthlyIrrfBracket> $brackets */
    public function __construct(
        private NormativeRuleMetadata $metadata,
        public array $brackets,
        public Money $dependentDeduction,
        public Money $simplifiedDeduction,
        public Money $fullReductionIncomeLimit,
        public Money $fullReductionCap,
        public Money $partialReductionIncomeLimit,
        public Money $partialReductionFixedAmount,
        public int $partialReductionCoefficientMillionths,
    ) {}

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return $this->metadata;
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->metadata->effectivePeriod;
    }
}
