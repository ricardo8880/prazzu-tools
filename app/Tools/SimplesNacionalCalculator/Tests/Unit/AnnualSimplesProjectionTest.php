<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use App\Tools\SimplesNacionalCalculator\Domain\Services\AnnualSimplesProjection;
use PHPUnit\Framework\TestCase;

final class AnnualSimplesProjectionTest extends TestCase
{
    public function test_it_projects_twelve_months_without_float_arithmetic(): void
    {
        $projection = new AnnualSimplesProjection(
            new SimplesNacionalCalculator(new SimplesNacionalTaxTable()),
        );

        $result = $projection->project(
            TaxAnnex::I,
            Money::fromDecimal('10000.00'),
            Percentage::fromString('10'),
        );

        self::assertCount(12, $result->months);
        self::assertSame('R$ 10.000,00', $result->months[0]->monthlyRevenue->formatPtBr());
        self::assertSame('R$ 11.000,00', $result->months[1]->monthlyRevenue->formatPtBr());
        self::assertSame('R$ 28.531,17', $result->months[11]->monthlyRevenue->formatPtBr());
        self::assertSame('R$ 213.842,85', $result->totalRevenue->formatPtBr());
    }
}
