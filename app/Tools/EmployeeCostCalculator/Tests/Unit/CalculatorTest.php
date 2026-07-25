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
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('3000'), Money::zero(), Money::fromDecimal('500'), 'general', Percentage::fromString('2'), Percentage::fromString('5.8')));
        self::assertSame('R$ 5.366,17', $r->summary[0]->value);
        self::assertSame('R$ 64.394,04', $r->summary[1]->value);
    }
}
