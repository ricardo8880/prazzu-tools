<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use PHPUnit\Framework\TestCase;

final class SimplesNacionalTaxTableTest extends TestCase
{
    public function test_every_annex_has_six_brackets(): void
    {
        $table = new SimplesNacionalTaxTable;

        foreach (TaxAnnex::cases() as $annex) {
            self::assertCount(6, $table->bracketsFor($annex));
        }
    }

    public function test_identifies_brackets_at_revenue_boundaries(): void
    {
        $table = new SimplesNacionalTaxTable;

        self::assertSame(1, $table->bracketFor(TaxAnnex::I, Money::fromDecimal('180000.00'))->number);
        self::assertSame(2, $table->bracketFor(TaxAnnex::I, Money::fromDecimal('180000.01'))->number);
        self::assertSame(6, $table->bracketFor(TaxAnnex::V, Money::fromDecimal('4800000.00'))->number);
    }

    public function test_rejects_revenue_above_simples_nacional_limit(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O RBT12 deve estar entre R$ 0,01 e R$ 4.800.000,00.');

        (new SimplesNacionalTaxTable)->bracketFor(
            TaxAnnex::I,
            Money::fromDecimal('4800000.01'),
        );
    }
}
