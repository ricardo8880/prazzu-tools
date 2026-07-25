<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tax\Normative;

use App\Core\Dates\ReferenceDate;
use App\Core\Tax\Normative\FactorRRule;
use App\Core\Tax\Normative\LateDasRule;
use App\Core\Tax\Normative\TaxRegimeComparisonRule;
use PHPUnit\Framework\TestCase;

final class TaxNormativeRulesTest extends TestCase
{
    public function test_rules_expose_versioned_official_snapshots(): void
    {
        $date = ReferenceDate::fromString('2026-07-25');

        self::assertSame('28', (new FactorRRule)->threshold()->toDecimalString());
        self::assertSame('0.33', (new LateDasRule)->dailyFine()->toDecimalString());
        self::assertSame('20', (new LateDasRule)->maximumFine()->toDecimalString());
        self::assertNotEmpty((new TaxRegimeComparisonRule)->snapshot($date)->references);
    }
}
