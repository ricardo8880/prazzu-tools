<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use App\Tools\VacationCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_returns_the_standardized_pto_result_contract(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            hourlyRate: '25.00',
            accruedPtoHours: '40',
            plannedPtoHours: '24',
            accrualHoursPerPayPeriod: '4',
            payPeriodsBeforeLeave: 2,
            payoutPercent: '100',
        ));

        self::assertSame('pto-calculator', $result->toolSlug);
        self::assertSame('2.0.0', $result->schemaVersion);
        self::assertSame('projected_hours', $result->summary[0]->key);
        self::assertSame('48 hours', $result->summary[0]->value);
        self::assertSame('$600.00', $result->summary[3]->value);
        self::assertSame(48_000, $result->details['projected_hours_thousandths']);
        self::assertSame(24_000, $result->details['remaining_hours_thousandths']);
    }
}
