<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Tools\TaxRegimeComparator\Application\Taxation\ActualProfitTaxEstimateProvider;
use App\Tools\TaxRegimeComparator\Domain\Rules\ActualProfitTaxRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ActualProfitTaxEstimateProviderTest extends TestCase
{
    public function test_estimates_actual_profit_with_explicit_credit_base(): void
    {
        $provider = new ActualProfitTaxEstimateProvider(new ActualProfitTaxRule);

        $result = $provider->estimate(new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2025-07-01'),
            activity: 'services',
            monthlyRevenue: Money::fromDecimal('100000'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000'),
            payrollLastTwelveMonths: Money::fromDecimal('240000'),
            indirectTaxRate: Percentage::fromString('5'),
            monthlyOperatingCosts: Money::fromDecimal('40000'),
            monthlyDeductibleExpenses: Money::fromDecimal('10000'),
            monthlyPisCofinsCreditBase: Money::fromDecimal('30000'),
        ));

        self::assertSame('lucro_real', $result->regime);
        self::assertSame(2647500, $result->monthlyTotal->minorAmount());
        self::assertSame(31770000, $result->annualTotal->minorAmount());
        self::assertCount(6, $result->items);
        self::assertSame(750000, $result->items[0]->monthlyAmount->minorAmount());
        self::assertSame(300000, $result->items[1]->monthlyAmount->minorAmount());
        self::assertSame(115500, $result->items[3]->monthlyAmount->minorAmount());
        self::assertSame(532000, $result->items[4]->monthlyAmount->minorAmount());
    }

    public function test_does_not_create_irpj_or_csll_when_estimated_result_is_negative(): void
    {
        $provider = new ActualProfitTaxEstimateProvider(new ActualProfitTaxRule);

        $result = $provider->estimate(new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2025-07-01'),
            activity: 'commerce',
            monthlyRevenue: Money::fromDecimal('50000'),
            revenueLastTwelveMonths: Money::fromDecimal('600000'),
            payrollLastTwelveMonths: Money::fromDecimal('120000'),
            indirectTaxRate: Percentage::fromString('8'),
            monthlyOperatingCosts: Money::fromDecimal('45000'),
            monthlyDeductibleExpenses: Money::fromDecimal('10000'),
            monthlyPisCofinsCreditBase: Money::fromDecimal('20000'),
        ));

        self::assertSame(0, $result->items[0]->monthlyAmount->minorAmount());
        self::assertSame(0, $result->items[1]->monthlyAmount->minorAmount());
        self::assertSame(0, $result->items[2]->monthlyAmount->minorAmount());
    }

    public function test_rejects_period_after_supported_transition_window(): void
    {
        $provider = new ActualProfitTaxEstimateProvider(new ActualProfitTaxRule);

        $request = new TaxEstimateRequest(
            referenceDate: new DateTimeImmutable('2027-01-01'),
            activity: 'services',
            monthlyRevenue: Money::fromDecimal('100000'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000'),
            payrollLastTwelveMonths: Money::fromDecimal('240000'),
            indirectTaxRate: Percentage::fromString('5'),
            monthlyOperatingCosts: Money::fromDecimal('40000'),
            monthlyDeductibleExpenses: Money::fromDecimal('10000'),
            monthlyPisCofinsCreditBase: Money::fromDecimal('30000'),
        );

        self::assertFalse($provider->supports($request));
    }
}
