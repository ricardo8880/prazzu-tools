<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SalesCommissionCalculator\Application\Data\CalculationInput;
use App\Tools\SalesCommissionCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_adds_the_goal_bonus_when_the_goal_is_reached(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('120000'),
            Percentage::fromString('3'),
            Money::fromDecimal('100000'),
            Percentage::fromString('1'),
        ));

        self::assertSame('R$ 4.800,00', $result->summary[0]->value);
        self::assertSame('R$ 3.600,00', $result->summary[1]->value);
        self::assertSame('R$ 1.200,00', $result->summary[2]->value);
        self::assertSame('120,00 %', $result->summary[3]->value);
    }
}
