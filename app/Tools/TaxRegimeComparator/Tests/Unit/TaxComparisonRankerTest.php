<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use App\Tools\TaxRegimeComparator\Domain\Services\TaxComparisonRanker;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TaxComparisonRankerTest extends TestCase
{
    public function test_it_orders_comparable_regimes_and_calculates_savings_against_second_place(): void
    {
        $result = (new TaxComparisonRanker)->rank(new DateTimeImmutable('2026-01-15'), [
            $this->available(TaxRegime::ActualProfit, '30000.00'),
            $this->available(TaxRegime::SimplesNacional, '10000.00'),
            $this->available(TaxRegime::PresumedProfit, '17500.00'),
        ]);

        self::assertSame(TaxRegime::SimplesNacional, $result->lowestEstimatedBurden);
        self::assertSame(3, $result->comparableRegimeCount);
        self::assertSame('7500.00', $this->decimal($result->estimatedMonthlySavings));
        self::assertSame('90000.00', $this->decimal($result->estimatedAnnualSavings));
        self::assertSame(TaxRegime::SimplesNacional, $result->ranking[0]->estimate->regime);
        self::assertSame(TaxRegime::PresumedProfit, $result->ranking[1]->estimate->regime);
        self::assertSame('7500.00', $this->decimal($result->ranking[1]->monthlyDifferenceFromLowest));
    }

    public function test_it_does_not_claim_a_winner_when_only_one_regime_is_comparable(): void
    {
        $result = (new TaxComparisonRanker)->rank(new DateTimeImmutable('2026-01-15'), [
            $this->available(TaxRegime::SimplesNacional, '10000.00'),
            $this->unavailable(TaxRegime::PresumedProfit),
            $this->unavailable(TaxRegime::ActualProfit),
        ]);

        self::assertNull($result->lowestEstimatedBurden);
        self::assertNull($result->estimatedMonthlySavings);
        self::assertSame(1, $result->comparableRegimeCount);
        self::assertCount(1, $result->ranking);
        self::assertStringContainsString('Apenas um regime', implode(' ', $result->warnings));
    }

    public function test_it_keeps_unavailable_estimates_out_of_the_ranking_and_preserves_their_warning(): void
    {
        $result = (new TaxComparisonRanker)->rank(new DateTimeImmutable('2026-01-15'), [
            $this->available(TaxRegime::SimplesNacional, '10000.00'),
            $this->available(TaxRegime::PresumedProfit, '17500.00'),
            new TaxRegimeEstimate(
                regime: TaxRegime::ActualProfit,
                status: EstimateStatus::InsufficientData,
                estimatedMonthlyTax: null,
                estimatedAnnualTax: null,
                warnings: ['Base de créditos não informada.'],
            ),
        ]);

        self::assertCount(2, $result->ranking);
        self::assertSame(2, $result->comparableRegimeCount);
        self::assertStringContainsString('Lucro Real: Base de créditos não informada.', implode(' ', $result->warnings));
    }

    private function available(TaxRegime $regime, string $monthly): TaxRegimeEstimate
    {
        $monthlyMoney = Money::fromDecimal($monthly);

        return new TaxRegimeEstimate(
            regime: $regime,
            status: EstimateStatus::Available,
            estimatedMonthlyTax: $monthlyMoney,
            estimatedAnnualTax: $monthlyMoney->multiply(12),
            assumptions: ['Cenário mensal repetido por doze meses.'],
        );
    }

    private function unavailable(TaxRegime $regime): TaxRegimeEstimate
    {
        return new TaxRegimeEstimate(
            regime: $regime,
            status: EstimateStatus::UnsupportedScenario,
            estimatedMonthlyTax: null,
            estimatedAnnualTax: null,
            warnings: ['Cenário indisponível.'],
        );
    }

    private function decimal(?Money $money): ?string
    {
        if ($money === null) {
            return null;
        }

        $minor = $money->minorAmount();
        $absolute = abs($minor);

        return ($minor < 0 ? '-' : '').intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }
}
