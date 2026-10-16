<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Tests\Unit;

use App\Tools\OvertimeCalculator\Application\Data\CalculationInput;
use App\Tools\OvertimeCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_time_and_a_half_without_bonus(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-10', '20.00', '40', '5'));

        self::assertSame('$20.00', $result->summary[0]->value);
        self::assertSame('$800.00', $result->summary[1]->value);
        self::assertSame('$150.00', $result->summary[2]->value);
        self::assertSame('$950.00', $result->summary[4]->value);
    }

    public function test_nondiscretionary_bonus_increases_only_the_overtime_premium_and_is_not_double_counted(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-10', '20.00', '40', '5', '1.5', '100.00'));

        self::assertSame('$22.22', $result->summary[0]->value);
        self::assertSame('$155.55', $result->summary[2]->value);
        self::assertSame('$100.00', $result->summary[3]->value);
        self::assertSame('$1,055.55', $result->summary[4]->value);
        self::assertSame(5_555, $result->details['overtime_premium_minor']);
    }
}
