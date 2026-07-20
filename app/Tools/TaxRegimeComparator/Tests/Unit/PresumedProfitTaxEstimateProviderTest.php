<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Tools\TaxRegimeComparator\Application\Taxation\PresumedProfitTaxEstimateProvider;
use App\Tools\TaxRegimeComparator\Domain\Rules\PresumedProfitTaxRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PresumedProfitTaxEstimateProviderTest extends TestCase
{
    public function test_estimates_service_with_explicit_iss_rate(): void
    {
        $provider = new PresumedProfitTaxEstimateProvider(new PresumedProfitTaxRule);

        $result = $provider->estimate(new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2025-07-01'),
            activity: 'services',
            monthlyRevenue: Money::fromDecimal('100000'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000'),
            payrollLastTwelveMonths: Money::fromDecimal('240000'),
            indirectTaxRate: Percentage::fromString('5'),
        ));

        self::assertSame('lucro_presumido', $result->regime);
        self::assertSame(1753000, $result->monthlyTotal->minorAmount());
        self::assertCount(6, $result->items);
        self::assertSame('IRPJ', $result->items[0]->code);
        self::assertSame('INDIRECT_TAXES', $result->items[5]->code);
    }

    public function test_rejects_2026_scenario_above_five_million_without_calendar_year_data(): void
    {
        $provider = new PresumedProfitTaxEstimateProvider(new PresumedProfitTaxRule);

        $request = new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2026-07-01'),
            activity: 'commerce',
            monthlyRevenue: Money::fromDecimal('500000'),
            revenueLastTwelveMonths: Money::fromDecimal('6000000'),
            payrollLastTwelveMonths: Money::fromDecimal('500000'),
            indirectTaxRate: Percentage::fromString('8'),
        );

        self::assertFalse($provider->supports($request));
    }
}
