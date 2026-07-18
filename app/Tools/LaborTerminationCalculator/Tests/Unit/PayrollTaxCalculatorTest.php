<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\PayrollTaxCalculator;
use PHPUnit\Framework\TestCase;

final class PayrollTaxCalculatorTest extends TestCase
{
    public function test_inss_uses_progressive_2026_brackets(): void
    {
        $tax = (new PayrollTaxCalculator)->inss(Money::fromDecimal('3.000,00'));
        self::assertSame(24858, $tax->minorAmount());
    }

    public function test_irrf_is_zero_for_income_up_to_five_thousand_after_2026_reduction(): void
    {
        $calculator = new PayrollTaxCalculator;
        $income = Money::fromDecimal('5.000,00');
        self::assertSame(0, $calculator->irrf($income, $calculator->inss($income), 0)->minorAmount());
    }
}
