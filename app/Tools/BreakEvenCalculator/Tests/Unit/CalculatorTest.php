<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\BreakEvenCalculator\Application\Data\CalculationInput;
use App\Tools\BreakEvenCalculator\Domain\Services\Calculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_the_first_whole_unit_that_covers_fixed_costs(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('10000'),
            Money::fromDecimal('100'),
            Money::fromDecimal('60'),
        ));

        self::assertSame('R$ 25.000,00', $result->summary[0]->value);
        self::assertSame('250 unidades', $result->summary[1]->value);
        self::assertSame('R$ 40,00', $result->summary[2]->value);
        self::assertSame('40,00 %', $result->summary[3]->value);
    }

    public function test_it_rejects_a_non_positive_contribution_margin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('1000'),
            Money::fromDecimal('50'),
            Money::fromDecimal('50'),
        ));
    }
}
