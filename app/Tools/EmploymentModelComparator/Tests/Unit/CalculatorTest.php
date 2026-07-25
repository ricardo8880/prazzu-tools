<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\EmploymentModelComparator\Application\Data\CalculationInput;
use App\Tools\EmploymentModelComparator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_compares_all_models(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('5000'), Money::fromDecimal('500'), Percentage::fromString('10'), Percentage::fromString('40'), Money::fromDecimal('7000'), Percentage::fromString('6'), Money::fromDecimal('300'), Money::fromDecimal('6000'), Percentage::fromString('20'), Percentage::fromString('20')));
        self::assertSame('R$ 5.000,00', $r->summary[0]->value);
        self::assertSame('R$ 6.280,00', $r->summary[1]->value);
        self::assertSame('R$ 4.800,00', $r->summary[2]->value);
    }
}
