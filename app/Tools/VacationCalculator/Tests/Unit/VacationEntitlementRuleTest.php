<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Tools\VacationCalculator\Domain\Rules\VacationEntitlementRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VacationEntitlementRuleTest extends TestCase
{
    #[DataProvider('absenceBands')]
    public function test_it_applies_the_clt_absence_bands(int $absences, int $expectedDays): void
    {
        self::assertSame($expectedDays, (new VacationEntitlementRule)->entitledDays($absences));
    }

    /** @return iterable<string, array{int, int}> */
    public static function absenceBands(): iterable
    {
        yield 'no absences' => [0, 30];
        yield 'upper 30 day boundary' => [5, 30];
        yield 'lower 24 day boundary' => [6, 24];
        yield 'upper 24 day boundary' => [14, 24];
        yield 'lower 18 day boundary' => [15, 18];
        yield 'upper 18 day boundary' => [23, 18];
        yield 'lower 12 day boundary' => [24, 12];
        yield 'upper 12 day boundary' => [32, 12];
        yield 'loss of entitlement' => [33, 0];
    }
}
