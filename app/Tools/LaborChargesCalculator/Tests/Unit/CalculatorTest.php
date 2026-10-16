<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\LaborChargesCalculator\Application\Data\CalculationInput;
use App\Tools\LaborChargesCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_standard_us_employer_cost_is_itemized(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('3000'), Money::fromDecimal('500'), 'standard', Percentage::fromString('2'), Percentage::fromString('5.8')));
        self::assertSame('$467.00', $r->summary[0]->value);
        self::assertSame('$5,604.00', $r->summary[1]->value);
        self::assertSame('$2,232.00', $r->summary[2]->value);
    }
}
