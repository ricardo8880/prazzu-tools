<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Tools\SimplesNacionalCalculator\Application\Taxation\SimplesNacionalTaxEstimateProvider;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SimplesNacionalTaxEstimateProviderTest extends TestCase
{
    public function test_maps_commerce_to_annex_one_and_returns_das(): void
    {
        $provider = new SimplesNacionalTaxEstimateProvider(
            new SimplesNacionalCalculator(new SimplesNacionalTaxTable),
            new FactorRCalculator,
        );

        $result = $provider->estimate(new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2026-07-01'),
            activity: 'commerce',
            monthlyRevenue: Money::fromDecimal('50000'),
            revenueLastTwelveMonths: Money::fromDecimal('600000'),
            payrollLastTwelveMonths: Money::fromDecimal('100000'),
        ));

        self::assertSame('simples_nacional', $result->regime);
        self::assertSame($result->monthlyTotal->multiply(12)->minorAmount(), $result->annualTotal->minorAmount());
        self::assertSame('DAS', $result->items[0]->code);
        self::assertStringContainsString('Anexo I', $result->assumptions[0]);
    }

    public function test_uses_factor_r_for_services(): void
    {
        $provider = new SimplesNacionalTaxEstimateProvider(
            new SimplesNacionalCalculator(new SimplesNacionalTaxTable),
            new FactorRCalculator,
        );

        $result = $provider->estimate(new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2026-07-01'),
            activity: 'services',
            monthlyRevenue: Money::fromDecimal('50000'),
            revenueLastTwelveMonths: Money::fromDecimal('600000'),
            payrollLastTwelveMonths: Money::fromDecimal('180000'),
        ));

        self::assertStringContainsString('Anexo III', $result->assumptions[0]);
    }
}
