<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Rules;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Money\Money;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeRuleMetadata;

final readonly class EmployeeSocialSecurityRule implements EffectiveDated, NormativeRule
{
    /** @param list<SocialSecurityBracket> $brackets */
    public function __construct(
        private NormativeRuleMetadata $metadata,
        public array $brackets,
        public Money $maximumContributionBase,
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
