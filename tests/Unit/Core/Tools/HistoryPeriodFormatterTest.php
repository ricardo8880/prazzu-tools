<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\History\Support\HistoryPeriodFormatter;
use PHPUnit\Framework\TestCase;

final class HistoryPeriodFormatterTest extends TestCase
{
    public function test_it_formats_month_and_quarter_contexts_without_locale_dependency(): void
    {
        self::assertSame('Agosto/2026', HistoryPeriodFormatter::yearMonth('2026-08'));
        self::assertSame('Agosto/2026', HistoryPeriodFormatter::yearMonth('2026-08-01'));
        self::assertSame('Agosto/2026', HistoryPeriodFormatter::monthNumber(8, 2026));
        self::assertSame('3º trimestre/2026', HistoryPeriodFormatter::quarter(3, 2026));
    }

    public function test_it_rejects_invalid_periods(): void
    {
        self::assertNull(HistoryPeriodFormatter::yearMonth('2026-13'));
        self::assertNull(HistoryPeriodFormatter::monthNumber(0, 2026));
        self::assertNull(HistoryPeriodFormatter::quarter(5, 2026));
    }
}
