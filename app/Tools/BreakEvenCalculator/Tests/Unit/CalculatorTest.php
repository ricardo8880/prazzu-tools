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

        self::assertSame('1.2.0', $result->schemaVersion);
        self::assertSame('R$ 25.000,00', $result->summary[0]->value);
        self::assertSame('250 unidades', $result->summary[1]->value);
        self::assertSame('R$ 40,00', $result->summary[2]->value);
        self::assertSame('40,00 %', $result->summary[3]->value);
        self::assertNotNull($result->calculationMemory);
        self::assertTrue($result->calculationMemory->isEstimate);
        self::assertNotEmpty($result->calculationMemory->steps);
        self::assertNotEmpty($result->calculationMemory->assumptions);
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

    public function test_it_exposes_the_rounding_surplus_at_the_first_whole_unit(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('100'), Money::fromDecimal('10'), Money::fromDecimal('7'),
        ));

        self::assertSame('34 unidades', $result->summary[1]->value);
        self::assertSame('R$ 2,00', $result->summary[4]->value);
    }

    public function test_zero_fixed_costs_returns_zero_units_with_explanatory_warning(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('0'), Money::fromDecimal('10'), Money::fromDecimal('7'),
        ));

        self::assertSame('0 unidades', $result->summary[1]->value);
        self::assertSame('no_fixed_costs', $result->warnings[0]->code);
    }
}
