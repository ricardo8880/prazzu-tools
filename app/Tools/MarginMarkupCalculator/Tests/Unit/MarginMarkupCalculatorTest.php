<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use PHPUnit\Framework\TestCase;

final class MarginMarkupCalculatorTest extends TestCase
{
    public function test_calculates_complete_sale_price_and_cost_composition(): void
    {
        $result = $this->calculator()->calculate(
            baseCost: Money::fromDecimal('100,00'),
            additionalCosts: Money::fromDecimal('10,00'),
            freightCost: Money::fromDecimal('5,00'),
            packagingCost: Money::fromDecimal('3,00'),
            fixedExpenses: Money::fromDecimal('2,00'),
            desiredMargin: Percentage::fromString('20'),
            taxes: Percentage::fromString('10'),
            commission: Percentage::fromString('5'),
            cardFees: Percentage::fromString('2'),
            marketplaceFees: Percentage::fromString('3'),
        );

        self::assertSame('R$ 200,00', $result->salePrice->formatPtBr());
        self::assertSame('R$ 120,00', $result->totalCost->formatPtBr());
        self::assertSame('R$ 80,00', $result->grossProfit->formatPtBr());
        self::assertSame('R$ 40,00', $result->netProfit->formatPtBr());
        self::assertSame('66.666667', $result->markup->toDecimalString());
        self::assertSame('1,6667', $result->markupMultiplier);
        self::assertSame('2.0.0', $result->ruleVersion);
    }

    public function test_rejects_zero_total_cost(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O custo total deve ser maior que zero.');

        $this->calculator()->calculate(
            Money::fromDecimal('0,00'), Money::zero(), Money::zero(), Money::zero(), Money::zero(),
            Percentage::fromString('25'), Percentage::zero(), Percentage::zero(), Percentage::zero(), Percentage::zero(),
        );
    }

    public function test_rejects_percentage_sum_equal_to_one_hundred_percent(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('A soma da margem, impostos, comissão e taxas deve ser menor que 100%.');

        $this->calculator()->calculate(
            Money::fromDecimal('100,00'), Money::zero(), Money::zero(), Money::zero(), Money::zero(),
            Percentage::fromString('50'), Percentage::fromString('30'), Percentage::fromString('20'), Percentage::zero(), Percentage::zero(),
        );
    }

    private function calculator(): MarginMarkupCalculator
    {
        return new MarginMarkupCalculator;
    }
}
