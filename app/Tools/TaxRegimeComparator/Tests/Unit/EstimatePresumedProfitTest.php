<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Services\InMemoryTaxEstimateProviderRegistry;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimatePresumedProfit;
use App\Tools\TaxRegimeComparator\Application\Taxation\PresumedProfitTaxEstimateProvider;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Rules\PresumedProfitTaxRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EstimatePresumedProfitTest extends TestCase
{
    public function test_returns_available_estimate_when_provider_and_indirect_rate_exist(): void
    {
        $registry = new InMemoryTaxEstimateProviderRegistry;
        $registry->register(new PresumedProfitTaxEstimateProvider(new PresumedProfitTaxRule));

        $estimate = (new EstimatePresumedProfit($registry))->execute($this->scenario(Percentage::fromString('5')));

        self::assertSame(EstimateStatus::Available, $estimate->status);
        self::assertTrue($estimate->isComparable());
        self::assertCount(6, $estimate->taxes);
    }

    public function test_requires_indirect_tax_rate(): void
    {
        $estimate = (new EstimatePresumedProfit(new InMemoryTaxEstimateProviderRegistry))
            ->execute($this->scenario());

        self::assertSame(EstimateStatus::InsufficientData, $estimate->status);
        self::assertFalse($estimate->isComparable());
    }

    private function scenario(?Percentage $rate = null): TaxComparisonScenario
    {
        return new TaxComparisonScenario(
            referenceDate: new DateTimeImmutable('2025-07-01'),
            businessActivity: BusinessActivity::Services,
            monthlyRevenue: Money::fromDecimal('100000'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000'),
            payrollLastTwelveMonths: Money::fromDecimal('240000'),
            monthlyOperatingCosts: Money::fromDecimal('20000'),
            monthlyDeductibleExpenses: Money::fromDecimal('10000'),
            indirectTaxRate: $rate,
            state: 'SP',
            municipality: 'São Paulo',
        );
    }
}
