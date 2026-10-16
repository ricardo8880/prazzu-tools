<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tax\Normative;

use App\Core\Dates\ReferenceDate;
use App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule;
use PHPUnit\Framework\TestCase;

final class TaxNormativeRulesTest extends TestCase
{
    public function test_rules_expose_versioned_official_snapshots(): void
    {
        $date = ReferenceDate::fromString('2026-07-25');

        self::assertSame(60720, MonthlyPersonalIncomeTaxRule::for2026()->simplifiedDeduction->minorAmount);
        self::assertSame('2026.1.0', MonthlyPersonalIncomeTaxRule::for2026()->snapshot($date)->version);
    }
}
