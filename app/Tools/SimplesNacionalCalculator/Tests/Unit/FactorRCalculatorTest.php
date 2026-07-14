<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use PHPUnit\Framework\TestCase;

final class FactorRCalculatorTest extends TestCase
{
    public function test_selects_annex_iii_when_factor_r_is_above_threshold(): void
    {
        $result = $this->calculator()->calculate(
            Money::fromDecimal('36000.00'),
            Money::fromDecimal('120000.00'),
        );

        self::assertSame('30', $result->factorR->toDecimalString());
        self::assertSame(TaxAnnex::III, $result->applicableAnnex);
        self::assertTrue($result->qualifiesForAnnexIII());
    }

    public function test_selects_annex_iii_at_exactly_twenty_eight_percent(): void
    {
        $result = $this->calculator()->calculate(
            Money::fromDecimal('28000.00'),
            Money::fromDecimal('100000.00'),
        );

        self::assertSame('28', $result->factorR->toDecimalString());
        self::assertSame(TaxAnnex::III, $result->applicableAnnex);
    }

    public function test_selects_annex_v_when_factor_r_is_below_threshold(): void
    {
        $result = $this->calculator()->calculate(
            Money::fromDecimal('27999.99'),
            Money::fromDecimal('100000.00'),
        );

        self::assertSame('27.99999', $result->factorR->toDecimalString());
        self::assertSame(TaxAnnex::V, $result->applicableAnnex);
        self::assertFalse($result->qualifiesForAnnexIII());
    }

    public function test_allows_zero_payroll_and_selects_annex_v(): void
    {
        $result = $this->calculator()->calculate(
            Money::zero(),
            Money::fromDecimal('100000.00'),
        );

        self::assertSame('0', $result->factorR->toDecimalString());
        self::assertSame(TaxAnnex::V, $result->applicableAnnex);
    }

    public function test_rejects_zero_rbt12(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O RBT12 deve ser maior que zero para calcular o Fator R.');

        $this->calculator()->calculate(Money::zero(), Money::zero());
    }

    public function test_rejects_negative_payroll(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('A folha de salários dos últimos 12 meses não pode ser negativa.');

        $this->calculator()->calculate(
            Money::fromDecimal('-1.00'),
            Money::fromDecimal('100000.00'),
        );
    }

    private function calculator(): FactorRCalculator
    {
        return new FactorRCalculator;
    }
}
