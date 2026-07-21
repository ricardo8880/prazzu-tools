<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Tools\FederalPaymentGuideGenerator\Domain\Services\GpsDueDateCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class GpsDueDateCalculatorTest extends TestCase
{
    public function test_company_due_date_is_advanced_when_day_twenty_is_weekend(): void
    {
        $date = (new GpsDueDateCalculator())->companyMonthly(new DateTimeImmutable('2026-05-01'));
        self::assertSame('2026-06-19', $date->format('Y-m-d'));
    }

    public function test_individual_due_date_is_postponed_when_day_fifteen_is_weekend(): void
    {
        $date = (new GpsDueDateCalculator())->individualMonthly(new DateTimeImmutable('2026-07-01'));
        self::assertSame('2026-08-17', $date->format('Y-m-d'));
    }
}
