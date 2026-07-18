<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\FeeAdjustmentCalculator;
use PHPUnit\Framework\TestCase;

final class FeeAdjustmentCalculatorTest extends TestCase
{
    public function test_it_calculates_positive_adjustment_in_cents(): void
    {
        $result = (new FeeAdjustmentCalculator)->calculate(
            Money::fromMinor(150000),
            Percentage::fromString('4.62'),
        );

        self::assertSame(6930, $result->difference->minorAmount());
        self::assertSame(156930, $result->adjustedValue->minorAmount());
        self::assertSame('4.62', $result->toArray()['percentage']);
    }

    public function test_it_supports_negative_adjustment(): void
    {
        $result = (new FeeAdjustmentCalculator)->calculate(
            Money::fromMinor(100000),
            Percentage::fromString('-2.5'),
        );

        self::assertSame(-2500, $result->difference->minorAmount());
        self::assertSame(97500, $result->adjustedValue->minorAmount());
    }

    public function test_it_rounds_fractional_cents_half_up(): void
    {
        $result = (new FeeAdjustmentCalculator)->calculate(
            Money::fromMinor(1),
            Percentage::fromString('50'),
        );

        self::assertSame(1, $result->difference->minorAmount());
        self::assertSame(2, $result->adjustedValue->minorAmount());
    }

    public function test_it_rejects_non_positive_current_value(): void
    {
        $this->expectException(InvalidValue::class);

        (new FeeAdjustmentCalculator)->calculate(
            Money::zero(),
            Percentage::fromString('5'),
        );
    }
}
