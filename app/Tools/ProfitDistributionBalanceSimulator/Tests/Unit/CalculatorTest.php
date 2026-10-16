<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Unit;

use App\Tools\ProfitDistributionBalanceSimulator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionBalanceSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_calculates_equity_and_cash_distribution_capacity(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            retainedEarnings: '40,000.00',
            currentYearNetIncome: '32,000.00',
            cashAvailable: '80,000.00',
            minimumCashReserve: '20,000.00',
            priorDistributions: '10,000.00',
            plannedDistribution: '50,000.00',
        ));

        self::assertSame(6_200_000, $result->details['equity_capacity_minor']);
        self::assertSame(6_000_000, $result->details['cash_capacity_minor']);
        self::assertSame(6_000_000, $result->details['recommended_ceiling_minor']);
        self::assertTrue($result->details['within_ceiling']);
    }

    public function test_builds_planning_rows(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            retainedEarnings: '20,000.00',
            currentYearNetIncome: '10,000.00',
            cashAvailable: '40,000.00',
            minimumCashReserve: '10,000.00',
            monthlyNetIncome: '3,000.00',
            planningMonths: 12,
        ));

        self::assertCount(12, $result->details['planning']['rows']);
        self::assertSame(3_000_00, $result->details['planning']['rows'][0]['projected_net_income_minor']);
    }
}
