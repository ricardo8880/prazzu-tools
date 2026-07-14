<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Data\TaxBracket;
use PHPUnit\Framework\TestCase;

final class TaxBracketTest extends TestCase
{
    public function test_it_identifies_revenue_inside_bracket(): void
    {
        $bracket = new TaxBracket(
            number: 1,
            revenueFrom: Money::fromDecimal('0'),
            revenueUntil: Money::fromDecimal('180000'),
            nominalRate: Percentage::fromString('4'),
            deduction: Money::zero(),
        );

        self::assertTrue($bracket->contains(Money::fromDecimal('120000')));
        self::assertFalse($bracket->contains(Money::fromDecimal('180000.01')));
    }
}
