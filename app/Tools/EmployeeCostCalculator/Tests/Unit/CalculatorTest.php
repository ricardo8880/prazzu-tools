<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\EmployeeCostCalculator\Application\Data\CalculationInput;
use App\Tools\EmployeeCostCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_monthly_and_annual_cost(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('3000'), Money::zero(), Money::fromDecimal('500'), 'standard', Percentage::fromString('2'), Percentage::fromString('5.8')));
        self::assertSame('$3,967.00', $r->summary[0]->value);
        self::assertSame('$47,604.00', $r->summary[1]->value);
    }
}
