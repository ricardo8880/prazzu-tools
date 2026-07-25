<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\FactorRSimulator\Application\Data\CalculationInput;
use App\Tools\FactorRSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_exactly_twenty_eight_percent_uses_annex_three(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('28000'),
            Money::fromDecimal('100000'),
        ));
        self::assertSame('28,00 %', $result->summary[0]->value);
        self::assertSame('Anexo III', $result->summary[1]->value);
        self::assertSame('R$ 0,00', $result->summary[3]->value);
    }

    public function test_below_twenty_eight_percent_uses_annex_five(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('20000'),
            Money::fromDecimal('100000'),
        ));
        self::assertSame('Anexo V', $result->summary[1]->value);
        self::assertSame('R$ 8.000,00', $result->summary[3]->value);
    }

    public function test_positive_payroll_with_zero_revenue_uses_official_twenty_eight_percent_rule(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('1000'),
            Money::zero(),
        ));

        self::assertSame('28,00 %', $result->summary[0]->value);
        self::assertSame('Anexo III', $result->summary[1]->value);
    }
}
