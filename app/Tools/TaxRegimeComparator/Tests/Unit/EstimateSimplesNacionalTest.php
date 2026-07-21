<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Contracts\TaxEstimateProvider;
use App\Core\Taxation\Data\TaxEstimateItem;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Core\Taxation\Data\TaxEstimateResult;
use App\Core\Taxation\Services\InMemoryTaxEstimateProviderRegistry;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateSimplesNacional;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EstimateSimplesNacionalTest extends TestCase
{
    public function test_estimates_commerce_without_importing_simples_classes_in_comparator(): void
    {
        $registry = new InMemoryTaxEstimateProviderRegistry;
        $registry->register(new class implements TaxEstimateProvider
        {
            public function regime(): string
            {
                return 'simples_nacional';
            }

            public function supports(TaxEstimateRequest $request): bool
            {
                return $request->activity === 'commerce';
            }

            public function estimate(TaxEstimateRequest $request): TaxEstimateResult
            {
                $monthly = Money::fromDecimal('4000');

                return new TaxEstimateResult(
                    regime: 'simples_nacional',
                    monthlyTotal: $monthly,
                    annualTotal: $monthly->multiply(12),
                    items: [new TaxEstimateItem('DAS', 'DAS', $monthly, $monthly->multiply(12), Percentage::fromString('8'))],
                );
            }
        });

        $estimate = (new EstimateSimplesNacional($registry))->execute($this->scenario(BusinessActivity::Commerce));

        self::assertSame(EstimateStatus::Available, $estimate->status);
        self::assertSame('simples_nacional', $estimate->regime->value);
        self::assertNotNull($estimate->estimatedMonthlyTax);
        self::assertCount(1, $estimate->taxes);
        self::assertSame('DAS', $estimate->taxes[0]->code);
    }

    public function test_returns_unsupported_status_when_no_provider_accepts_scenario(): void
    {
        $estimate = (new EstimateSimplesNacional(new InMemoryTaxEstimateProviderRegistry))
            ->execute($this->scenario(BusinessActivity::Mixed));

        self::assertSame(EstimateStatus::UnsupportedScenario, $estimate->status);
        self::assertFalse($estimate->isComparable());
    }

    private function scenario(BusinessActivity $activity): TaxComparisonScenario
    {
        return new TaxComparisonScenario(
            referenceDate: new DateTimeImmutable('2026-07-01'),
            businessActivity: $activity,
            monthlyRevenue: Money::fromDecimal('50000'),
            revenueLastTwelveMonths: Money::fromDecimal('600000'),
            payrollLastTwelveMonths: Money::fromDecimal('180000'),
            monthlyOperatingCosts: Money::fromDecimal('10000'),
            monthlyDeductibleExpenses: Money::fromDecimal('5000'),
            state: 'SP',
            municipality: 'São Paulo',
        );
    }
}
