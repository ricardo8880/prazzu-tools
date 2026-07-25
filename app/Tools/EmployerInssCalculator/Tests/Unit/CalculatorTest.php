<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\EmployerInssCalculator\Application\Data\CalculationInput;
use App\Tools\EmployerInssCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_general_regime_includes_cpp_rat_and_third_parties(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('100000'), 'general', Percentage::fromString('2'), Percentage::fromString('5.8'),
        ));
        self::assertSame('R$ 27.800,00', $result->summary[0]->value);
        self::assertSame('R$ 20.000,00', $result->summary[1]->value);
    }

    public function test_simples_outside_annex_four_has_cpp_inside_das(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('100000'), 'simples_other', Percentage::fromString('3'), Percentage::fromString('5.8'),
        ));
        self::assertSame('R$ 0,00', $result->summary[0]->value);
    }
}
