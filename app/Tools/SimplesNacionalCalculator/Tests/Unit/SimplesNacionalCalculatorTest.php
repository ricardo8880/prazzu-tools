<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use PHPUnit\Framework\TestCase;

final class SimplesNacionalCalculatorTest extends TestCase
{
    public function test_calculates_annex_i_effective_rate_and_estimated_das(): void
    {
        $result = $this->calculator()->calculate(
            TaxAnnex::I,
            Money::fromDecimal('300000.00'),
            Money::fromDecimal('25000.00'),
        );

        self::assertSame(2, $result->bracket->number);
        self::assertSame('7.3', $result->bracket->nominalRate->toDecimalString());
        self::assertSame('R$ 5.940,00', $result->bracket->deduction->formatPtBr());
        self::assertSame('5.32', $result->effectiveRate->toDecimalString());
        self::assertSame('R$ 1.330,00', $result->estimatedDas->formatPtBr());
        self::assertSame(SimplesNacionalTaxTable::RULE_VERSION, $result->ruleVersion);
    }

    public function test_calculates_annex_v_with_six_decimal_places_when_needed(): void
    {
        $result = $this->calculator()->calculate(
            TaxAnnex::V,
            Money::fromDecimal('500000.00'),
            Money::fromDecimal('40000.00'),
        );

        self::assertSame(3, $result->bracket->number);
        self::assertSame('17.52', $result->effectiveRate->toDecimalString());
        self::assertSame('R$ 7.008,00', $result->estimatedDas->formatPtBr());
    }

    public function test_allows_zero_monthly_revenue(): void
    {
        $result = $this->calculator()->calculate(
            TaxAnnex::III,
            Money::fromDecimal('100000.00'),
            Money::zero(),
        );

        self::assertSame('R$ 0,00', $result->estimatedDas->formatPtBr());
    }

    public function test_rejects_zero_rbt12(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O RBT12 deve ser maior que zero.');

        $this->calculator()->calculate(
            TaxAnnex::I,
            Money::zero(),
            Money::fromDecimal('1000.00'),
        );
    }

    private function calculator(): SimplesNacionalCalculator
    {
        return new SimplesNacionalCalculator(new SimplesNacionalTaxTable);
    }
}
