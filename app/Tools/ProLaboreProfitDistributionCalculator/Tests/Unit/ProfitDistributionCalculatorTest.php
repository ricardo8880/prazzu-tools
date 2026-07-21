<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProfitDistributionCalculator;
use PHPUnit\Framework\TestCase;

final class ProfitDistributionCalculatorTest extends TestCase
{
    public function test_calculates_available_profit_and_proportional_distribution(): void
    {
        $result = (new ProfitDistributionCalculator)->calculate(new ProfitDistributionInput(
            accountingProfit: Money::fromMinor(1_000_000),
            accumulatedLosses: Money::fromMinor(100_000),
            reservesAndUnavailableAmounts: Money::fromMinor(50_000),
            adjustments: Money::fromMinor(20_000),
            priorDistributions: Money::fromMinor(70_000),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [
                new PartnerProfitShare('a', Percentage::fromString('60')),
                new PartnerProfitShare('b', Percentage::fromString('40')),
            ],
            intendedDistribution: Money::fromMinor(600_000),
        ));

        self::assertSame(800_000, $result->maximumAvailableProfit->minorAmount());
        self::assertSame(600_000, $result->distributedAmount->minorAmount());
        self::assertSame(200_000, $result->undistributedBalance->minorAmount());
        self::assertSame(360_000, $result->partners[0]->distributedAmount->minorAmount());
        self::assertSame(240_000, $result->partners[1]->distributedAmount->minorAmount());
    }

    public function test_defined_amounts_must_equal_intended_distribution(): void
    {
        $this->expectException(InvalidValue::class);

        (new ProfitDistributionCalculator)->calculate(new ProfitDistributionInput(
            accountingProfit: Money::fromMinor(500_000),
            accumulatedLosses: Money::zero(),
            reservesAndUnavailableAmounts: Money::zero(),
            adjustments: Money::zero(),
            priorDistributions: Money::zero(),
            criterion: ProfitDistributionCriterion::DefinedAmounts,
            partners: [
                new PartnerProfitShare('a', Percentage::fromString('50'), Money::fromMinor(200_000)),
                new PartnerProfitShare('b', Percentage::fromString('50'), Money::fromMinor(200_000)),
            ],
            intendedDistribution: Money::fromMinor(500_000),
        ));
    }

    public function test_distribution_cannot_exceed_available_profit(): void
    {
        $this->expectException(InvalidValue::class);

        (new ProfitDistributionCalculator)->calculate(new ProfitDistributionInput(
            accountingProfit: Money::fromMinor(100_000),
            accumulatedLosses: Money::zero(),
            reservesAndUnavailableAmounts: Money::zero(),
            adjustments: Money::zero(),
            priorDistributions: Money::zero(),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [new PartnerProfitShare('a', Percentage::fromString('100'))],
            intendedDistribution: Money::fromMinor(100_001),
        ));
    }

    public function test_ownership_must_total_one_hundred_percent(): void
    {
        $this->expectException(InvalidValue::class);

        (new ProfitDistributionCalculator)->calculate(new ProfitDistributionInput(
            accountingProfit: Money::fromMinor(100_000),
            accumulatedLosses: Money::zero(),
            reservesAndUnavailableAmounts: Money::zero(),
            adjustments: Money::zero(),
            priorDistributions: Money::zero(),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [new PartnerProfitShare('a', Percentage::fromString('90'))],
        ));
    }

    public function test_negative_available_profit_becomes_zero_with_warning(): void
    {
        $result = (new ProfitDistributionCalculator)->calculate(new ProfitDistributionInput(
            accountingProfit: Money::fromMinor(100_000),
            accumulatedLosses: Money::fromMinor(120_000),
            reservesAndUnavailableAmounts: Money::zero(),
            adjustments: Money::zero(),
            priorDistributions: Money::zero(),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [new PartnerProfitShare('a', Percentage::fromString('100'))],
        ));

        self::assertSame(0, $result->maximumAvailableProfit->minorAmount());
        self::assertSame(0, $result->distributedAmount->minorAmount());
        self::assertNotEmpty($result->warnings);
    }
}
