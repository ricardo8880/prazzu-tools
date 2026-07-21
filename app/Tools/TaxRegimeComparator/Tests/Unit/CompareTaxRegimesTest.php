<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Contracts\TaxEstimateProvider;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Core\Taxation\Data\TaxEstimateResult;
use App\Core\Taxation\Services\InMemoryTaxEstimateProviderRegistry;
use App\Tools\TaxRegimeComparator\Application\Actions\CompareTaxRegimes;
use App\Tools\TaxRegimeComparator\Application\Data\TaxComparisonInput;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateActualProfit;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimatePresumedProfit;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateSimplesNacional;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use App\Tools\TaxRegimeComparator\Domain\Services\TaxComparisonRanker;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CompareTaxRegimesTest extends TestCase
{
    public function test_it_orchestrates_all_estimators_and_returns_the_ranked_comparison(): void
    {
        $registry = new InMemoryTaxEstimateProviderRegistry;
        $registry->register($this->provider(TaxRegime::SimplesNacional, '10000.00'));
        $registry->register($this->provider(TaxRegime::PresumedProfit, '17500.00'));
        $registry->register($this->provider(TaxRegime::ActualProfit, '26000.00'));

        $action = new CompareTaxRegimes(
            new EstimateSimplesNacional($registry),
            new EstimatePresumedProfit($registry),
            new EstimateActualProfit($registry),
            new TaxComparisonRanker,
        );

        $result = $action->execute(new TaxComparisonInput(
            referenceDate: new DateTimeImmutable('2026-01-15'),
            businessActivity: BusinessActivity::Services,
            monthlyRevenue: Money::fromDecimal('100000.00'),
            revenueLastTwelveMonths: Money::fromDecimal('1200000.00'),
            payrollLastTwelveMonths: Money::fromDecimal('400000.00'),
            monthlyOperatingCosts: Money::fromDecimal('20000.00'),
            monthlyDeductibleExpenses: Money::fromDecimal('15000.00'),
            monthlyPisCofinsCreditBase: Money::fromDecimal('10000.00'),
            indirectTaxRate: Percentage::fromString('5'),
        ));

        self::assertCount(3, $result->estimates);
        self::assertCount(3, $result->ranking);
        self::assertSame(TaxRegime::SimplesNacional, $result->lowestEstimatedBurden);
        self::assertSame(750000, $result->estimatedMonthlySavings?->minorAmount());
        self::assertSame('0.6.0', $result->ruleVersion);
    }

    private function provider(TaxRegime $regime, string $monthly): TaxEstimateProvider
    {
        return new class($regime, Money::fromDecimal($monthly)) implements TaxEstimateProvider
        {
            public function __construct(
                private readonly TaxRegime $taxRegime,
                private readonly Money $monthly,
            ) {}

            public function regime(): string
            {
                return $this->taxRegime->value;
            }

            public function supports(TaxEstimateRequest $request): bool
            {
                return true;
            }

            public function estimate(TaxEstimateRequest $request): TaxEstimateResult
            {
                return new TaxEstimateResult(
                    regime: $this->taxRegime->value,
                    monthlyTotal: $this->monthly,
                    annualTotal: $this->monthly->multiply(12),
                    items: [],
                );
            }
        };
    }
}
