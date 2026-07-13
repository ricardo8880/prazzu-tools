<?php

namespace App\Tools\MarginMarkupCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use PHPUnit\Framework\TestCase;

final class MarginMarkupCalculatorTest extends TestCase
{
    public function test_calculates_sale_price_margin_and_markup(): void
    {
        $result = (new MarginMarkupCalculator())->calculate(Money::fromDecimal('100,00'), Money::fromDecimal('20,00'), Percentage::fromString('25'));

        self::assertSame('R$ 160,00', $result->salePrice->formatPtBr());
        self::assertSame('R$ 40,00', $result->profit->formatPtBr());
        self::assertSame('33.333333', $result->markup->toDecimalString());
    }
}
