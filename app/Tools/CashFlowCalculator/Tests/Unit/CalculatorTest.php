<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\CashFlowCalculator\Application\Data\CalculationInput;
use App\Tools\CashFlowCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_monthly_cash_flow(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('10000'), Money::fromDecimal('50000'), Money::fromDecimal('5000'),
            Money::fromDecimal('25000'), Money::fromDecimal('5000'), Money::fromDecimal('3000'),
            Money::fromDecimal('2000'), Money::fromDecimal('1000'),
        ));

        self::assertSame('1.1.0', $result->schemaVersion);
        self::assertSame('R$ 29.000,00', $result->summary[0]->value);
        self::assertSame('R$ 19.000,00', $result->summary[1]->value);
        self::assertSame('R$ 55.000,00', $result->summary[2]->value);
        self::assertSame('R$ 36.000,00', $result->summary[3]->value);
        self::assertSame('R$ 20.000,00', $result->summary[4]->value);
        self::assertNotNull($result->calculationMemory);
        self::assertTrue($result->calculationMemory->isEstimate);
        self::assertNotEmpty($result->calculationMemory->steps);
        self::assertNotEmpty($result->calculationMemory->assumptions);
    }
}
