<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\FeeAdjustmentCalculator;
use PHPUnit\Framework\TestCase;

final class FeeAdjustmentCalculatorTest extends TestCase
{
    public function test_it_calculates_positive_adjustment_in_cents(): void
    {
        $result = (new FeeAdjustmentCalculator())->calculate(150000, 4.62);

        self::assertSame(6930, $result->differenceCents);
        self::assertSame(156930, $result->adjustedValueCents);
    }

    public function test_it_supports_negative_adjustment(): void
    {
        $result = (new FeeAdjustmentCalculator())->calculate(100000, -2.5);

        self::assertSame(-2500, $result->differenceCents);
        self::assertSame(97500, $result->adjustedValueCents);
    }

    public function test_it_rejects_non_positive_current_value(): void
    {
        $this->expectException(InvalidValue::class);

        (new FeeAdjustmentCalculator())->calculate(0, 5);
    }
}
