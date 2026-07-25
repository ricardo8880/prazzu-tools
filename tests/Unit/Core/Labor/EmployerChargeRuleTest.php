<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Labor;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Money\Percentage;
use PHPUnit\Framework\TestCase;

final class EmployerChargeRuleTest extends TestCase
{
    public function test_general_regime_keeps_cpp_rat_and_third_parties(): void
    {
        $rates = (new EmployerChargeRule())->ratesFor(
            'general', Percentage::fromString('2'), Percentage::fromString('5.8'),
        );

        self::assertSame(['fgts' => '8', 'cpp' => '20', 'rat' => '2', 'third_parties' => '5.8'], $rates->toArray());
    }

    public function test_simples_other_embeds_patronal_charges_and_keeps_fgts(): void
    {
        $rates = (new EmployerChargeRule())->ratesFor(
            'simples_other', Percentage::fromString('3'), Percentage::fromString('5.8'),
        );

        self::assertSame(['fgts' => '8', 'cpp' => '0', 'rat' => '0', 'third_parties' => '0'], $rates->toArray());
    }

    public function test_snapshot_preserves_official_sources(): void
    {
        $snapshot = (new EmployerChargeRule())->snapshot(ReferenceDate::fromString('2026-07-25'));

        self::assertSame('labor.employer-charges', $snapshot->identifier);
        self::assertCount(3, $snapshot->references);
    }
}
