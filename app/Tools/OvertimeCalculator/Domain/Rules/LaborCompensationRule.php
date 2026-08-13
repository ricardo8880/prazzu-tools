<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Rules;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeRuleMetadata;

final readonly class LaborCompensationRule implements EffectiveDated, NormativeRule
{
    public function __construct(
        private NormativeRuleMetadata $metadata,
        public Percentage $minimumOvertimePremium,
        public Percentage $minimumNightPremium,
        public int $reducedNightHourSeconds,
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
