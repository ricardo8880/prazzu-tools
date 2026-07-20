<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;
use PHPUnit\Framework\TestCase;

final class TaxRegimeEstimateTest extends TestCase
{
    public function test_available_estimate_with_totals_is_comparable(): void
    {
        $estimate = new TaxRegimeEstimate(
            regime: TaxRegime::SimplesNacional,
            status: EstimateStatus::Available,
            estimatedMonthlyTax: Money::fromDecimal('3000.00'),
            estimatedAnnualTax: Money::fromDecimal('36000.00'),
        );

        self::assertTrue($estimate->isComparable());
    }

    public function test_available_estimate_requires_monthly_and_annual_totals(): void
    {
        $this->expectException(\App\Core\Exceptions\InvalidValue::class);
        $this->expectExceptionMessage('Uma estimativa disponível deve informar os totais mensal e anual.');

        new TaxRegimeEstimate(
            regime: TaxRegime::SimplesNacional,
            status: EstimateStatus::Available,
            estimatedMonthlyTax: null,
            estimatedAnnualTax: null,
        );
    }

    public function test_unavailable_estimate_rejects_totals(): void
    {
        $this->expectException(\App\Core\Exceptions\InvalidValue::class);
        $this->expectExceptionMessage('Uma estimativa indisponível não pode informar totais tributários.');

        new TaxRegimeEstimate(
            regime: TaxRegime::ActualProfit,
            status: EstimateStatus::InsufficientData,
            estimatedMonthlyTax: Money::zero(),
            estimatedAnnualTax: Money::zero(),
        );
    }

    public function test_unavailable_estimate_is_not_comparable(): void
    {
        $estimate = new TaxRegimeEstimate(
            regime: TaxRegime::ActualProfit,
            status: EstimateStatus::InsufficientData,
            estimatedMonthlyTax: null,
            estimatedAnnualTax: null,
        );

        self::assertFalse($estimate->isComparable());
    }
}
