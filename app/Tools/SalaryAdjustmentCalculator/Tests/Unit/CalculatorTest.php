<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SalaryAdjustmentCalculator\Application\Data\CalculationInput;
use App\Tools\SalaryAdjustmentCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_percentage_fixed_and_retroactive_adjustments(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('3000'),
            Percentage::fromString('5'),
            Money::fromDecimal('50'),
            3,
        ));

        self::assertSame('R$ 3.200,00', $result->summary[0]->value);
        self::assertSame('R$ 200,00', $result->summary[1]->value);
        self::assertSame('6,66 %', $result->summary[2]->value);
        self::assertSame('R$ 600,00', $result->summary[3]->value);
        self::assertSame('R$ 2.666,67', $result->summary[4]->value);
        self::assertNotNull($result->calculationMemory);
        self::assertNotEmpty($result->calculationMemory->steps);
    }

    public function test_it_exposes_effective_adjustment_when_fixed_addition_is_present(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('3000'),
            Percentage::fromString('5'),
            Money::fromDecimal('50'),
            0,
        ));

        self::assertSame('6,66 %', $result->summary[2]->value);
    }
}
