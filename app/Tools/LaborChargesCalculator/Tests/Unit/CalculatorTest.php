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
    public function test_general_regime_cost_is_itemized(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('3000'), Money::fromDecimal('500'), 'general', Percentage::fromString('2'), Percentage::fromString('5.8')));
        self::assertSame('R$ 5.366,17', $r->summary[0]->value);
        self::assertSame('R$ 583,33', $r->summary[1]->value);
        self::assertSame('R$ 286,67', $r->summary[2]->value);
    }
}
