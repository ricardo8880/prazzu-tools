<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class LaborTerminationCalculatorTest extends TestCase
{
    public function test_final_pay_uses_decimal_hours_without_float_arithmetic(): void
    {
        $result = (new LaborTerminationCalculator)->calculate(
            new DateTimeImmutable('2026-10-16'), 'layoff', Money::fromDecimal('25.00'), '8.5', '4', Percentage::fromString('100'),
            Money::fromDecimal('1000.00'), '2.5', Money::zero(), Money::zero(), Money::zero(), Money::zero(), Money::zero(),
        );

        self::assertSame('$212.50', $result->regularPayDue->formatEnUs());
        self::assertSame('$100.00', $result->ptoPayout->formatEnUs());
        self::assertSame('$2,500.00', $result->severancePay->formatEnUs());
        self::assertSame('$2,812.50', $result->netFinalPay->formatEnUs());
    }
}
