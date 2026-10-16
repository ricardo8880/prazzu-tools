<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Labor;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Money\Percentage;
use PHPUnit\Framework\TestCase;

final class EmployerChargeRuleTest extends TestCase
{
    public function test_standard_profile_keeps_federal_and_user_entered_employer_rates(): void
    {
        $rates = (new EmployerChargeRule)->ratesFor('standard', Percentage::fromString('2'), Percentage::fromString('1.5'));
        self::assertSame(['social_security'=>'6.2','medicare'=>'1.45','futa'=>'0.6','suta'=>'2','other_employer_costs'=>'1.5'], $rates->toArray());
    }

    public function test_futa_exempt_profile_zeroes_futa_only(): void
    {
        $rates = (new EmployerChargeRule)->ratesFor('futa_exempt', Percentage::fromString('3'), Percentage::fromString('1'));
        self::assertSame('0', $rates->futa->toDecimalString());
        self::assertSame('3', $rates->suta->toDecimalString());
    }

    public function test_snapshot_preserves_us_sources(): void
    {
        $snapshot = (new EmployerChargeRule)->snapshot(ReferenceDate::fromString('2026-07-25'));
        self::assertSame('us.employer-payroll-2026', $snapshot->identifier);
        self::assertCount(2, $snapshot->references);
    }
}
