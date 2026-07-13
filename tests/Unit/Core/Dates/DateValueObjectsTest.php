<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Dates;

use App\Core\Dates\Competence;
use App\Core\Dates\DatePeriod;
use App\Core\Dates\ReferenceDate;
use PHPUnit\Framework\TestCase;

final class DateValueObjectsTest extends TestCase
{
    public function test_competence_exposes_its_boundaries(): void
    {
        $competence = Competence::fromString('2024-02');

        self::assertSame('2024-02-01', $competence->firstDay()->toString());
        self::assertSame('2024-02-29', $competence->lastDay()->toString());
    }

    public function test_period_is_inclusive(): void
    {
        $period = new DatePeriod(
            ReferenceDate::fromString('2026-01-01'),
            ReferenceDate::fromString('2026-01-31'),
        );

        self::assertTrue($period->contains(ReferenceDate::fromString('2026-01-31')));
        self::assertFalse($period->contains(ReferenceDate::fromString('2026-02-01')));
    }
}
