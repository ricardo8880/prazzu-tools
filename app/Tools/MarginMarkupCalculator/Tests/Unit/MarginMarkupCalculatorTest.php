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
    public function test_calculates_sale_price_margin_and_markup(): void
    {
        $result = (new MarginMarkupCalculator)->calculate(
            Money::fromDecimal('100,00'),
            Money::fromDecimal('20,00'),
            Percentage::fromString('25'),
        );

        self::assertSame('R$ 160,00', $result->salePrice->formatPtBr());
        self::assertSame('R$ 40,00', $result->profit->formatPtBr());
        self::assertSame('33.333333', $result->markup->toDecimalString());
        self::assertSame('1.0.0', $result->ruleVersion);
    }

    public function test_rejects_zero_total_cost(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O custo total deve ser maior que zero.');

        (new MarginMarkupCalculator)->calculate(
            Money::fromDecimal('0,00'),
            Money::fromDecimal('0,00'),
            Percentage::fromString('25'),
        );
    }

    public function test_rejects_margin_equal_to_one_hundred_percent(): void
    {
        $this->expectException(InvalidValue::class);

        (new MarginMarkupCalculator)->calculate(
            Money::fromDecimal('100,00'),
            Money::fromDecimal('0,00'),
            Percentage::fromString('100'),
        );
    }
}
