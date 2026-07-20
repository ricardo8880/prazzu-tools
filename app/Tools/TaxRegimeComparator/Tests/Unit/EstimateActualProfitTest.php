<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Services\InMemoryTaxEstimateProviderRegistry;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateActualProfit;
use App\Tools\TaxRegimeComparator\Application\Taxation\ActualProfitTaxEstimateProvider;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Rules\ActualProfitTaxRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EstimateActualProfitTest extends TestCase
{
    public function test_returns_available_estimate_when_required_data_exists(): void
    {
        $registry = new InMemoryTaxEstimateProviderRegistry;
        $registry->register(new ActualProfitTaxEstimateProvider(new ActualProfitTaxRule));

        $estimate = (new EstimateActualProfit($registry))->execute($this->scenario(
            Percentage::fromString('5'),
            Money::fromDecimal('30000'),
        ));

        self::assertSame(EstimateStatus::Available, $estimate->status);
        self::assertTrue($estimate->isComparable());
        self::assertSame(2647500, $estimate->estimatedMonthlyTax?->minorAmount());
        self::assertCount(6, $estimate->taxes);
    }

    public function test_requires_indirect_rate_and_credit_base(): void
    {
        $estimate = (new EstimateActualProfit(new InMemoryTaxEstimateProviderRegistry))
            ->execute($this->scenario());

        self::assertSame(EstimateStatus::InsufficientData, $estimate->status);
        self::assertFalse($estimate->isComparable());
    }

    private function scenario(?Percentage $rate = null, ?Money $creditBase = null): TaxComparisonScenario
    {
        return new TaxComparisonScenario(
            referenceDate: new DateTimeImmutable('2025-07-01'),
            businessActivity: BusinessActivity::Services,
            monthlyRevenue: Money::fromDecimal('100000'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000'),
            payrollLastTwelveMonths: Money::fromDecimal('240000'),
            monthlyOperatingCosts: Money::fromDecimal('40000'),
            monthlyDeductibleExpenses: Money::fromDecimal('10000'),
            monthlyPisCofinsCreditBase: $creditBase,
            indirectTaxRate: $rate,
            state: 'SP',
            municipality: 'São Paulo',
        );
    }
}
