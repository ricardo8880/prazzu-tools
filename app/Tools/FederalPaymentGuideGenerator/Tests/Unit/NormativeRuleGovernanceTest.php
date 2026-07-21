<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\Dates\ReferenceDate;
use App\Core\Dates\Exceptions\NoEffectiveRule;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleVersion;
use App\Tools\FederalPaymentGuideGenerator\Domain\Rules\LatePaymentRule;
use App\Tools\FederalPaymentGuideGenerator\Domain\Rules\RuleCatalog;
use PHPUnit\Framework\TestCase;

final class NormativeRuleGovernanceTest extends TestCase
{
    public function test_resolves_rule_by_due_date_and_exposes_official_sources(): void
    {
        $rule = (new NormativeRuleResolver)->resolveCurrent(
            RuleCatalog::latePaymentCharges(),
            RuleCatalog::LATE_PAYMENT_IDENTIFIER,
            ReferenceDate::fromString('2026-01-10'),
        );

        self::assertInstanceOf(LatePaymentRule::class, $rule);
        self::assertSame(RuleCatalog::CURRENT_VERSION, $rule->normativeMetadata()->version->value);
        self::assertCount(3, $rule->normativeMetadata()->references);

        foreach ($rule->normativeMetadata()->references as $reference) {
            self::assertNotNull($reference->officialUrl);
        }
    }

    public function test_recovers_exact_historical_version(): void
    {
        $rule = (new NormativeRuleResolver)->resolveHistorical(
            RuleCatalog::latePaymentCharges(),
            RuleCatalog::LATE_PAYMENT_IDENTIFIER,
            new NormativeRuleVersion(RuleCatalog::CURRENT_VERSION),
            ReferenceDate::fromString('2026-01-10'),
        );

        self::assertSame(RuleCatalog::CURRENT_VERSION, $rule->normativeMetadata()->version->value);
    }

    public function test_rejects_date_before_supported_normative_period(): void
    {
        $this->expectException(NoEffectiveRule::class);

        (new NormativeRuleResolver)->resolveCurrent(
            RuleCatalog::latePaymentCharges(),
            RuleCatalog::LATE_PAYMENT_IDENTIFIER,
            ReferenceDate::fromString('2009-05-27'),
        );
    }
}
