<?php

declare(strict_types=1);

namespace App\Core\Labor\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use App\Core\Tax\US\FederalPayroll2026;
use InvalidArgumentException;

/** Federal employer payroll rates for calendar year 2026. */
final readonly class EmployerChargeRule implements NormativeRule
{
    public const VERSION = '2026.1.0';

    public function ratesFor(string $profile, Percentage $sutaRate, Percentage $otherEmployerCosts): EmployerChargeRates
    {
        if (! in_array($profile, ['standard', 'futa_exempt'], true)) {
            throw new InvalidArgumentException('Unsupported employer payroll profile.');
        }

        return new EmployerChargeRates(
            socialSecurity: FederalPayroll2026::socialSecurityRate(),
            medicare: FederalPayroll2026::medicareRate(),
            futa: $profile === 'futa_exempt' ? Percentage::zero() : FederalPayroll2026::futaEffectiveRate(),
            suta: $sutaRate,
            otherEmployerCosts: $otherEmployerCosts,
        );
    }

    public function snapshot(ReferenceDate $referenceDate): NormativeRuleSnapshot
    {
        return NormativeRuleSnapshot::fromRule($this, $referenceDate);
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->normativeMetadata()->effectivePeriod;
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'us.employer-payroll-2026',
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: EffectivePeriod::from('2026-01-01', '2026-12-31'),
            references: [
                new NormativeReference(
                    type: NormativeSourceType::Other,
                    identifier: 'irs-pub-15a-2026',
                    title: 'IRS Publication 15-A (2026) — Employer’s Supplemental Tax Guide',
                    publishedAt: ReferenceDate::fromString('2026-01-01'),
                    officialUrl: 'https://www.irs.gov/publications/p15a',
                ),
                new NormativeReference(
                    type: NormativeSourceType::Other,
                    identifier: 'irs-topic-751',
                    title: 'IRS Topic No. 751 — Social Security and Medicare withholding rates',
                    publishedAt: ReferenceDate::fromString('2026-01-01'),
                    officialUrl: 'https://www.irs.gov/taxtopics/tc751',
                ),
            ],
            verifiedAt: ReferenceDate::fromString('2026-10-16'),
            verifiedBy: 'Prazzu Tools',
        );
    }
}
