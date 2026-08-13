<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Tests\Unit;

use App\Tools\MeiToMicroenterpriseSimulator\Application\Data\CalculationInput;
use App\Tools\MeiToMicroenterpriseSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_marks_projection_within_mei_limit(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('70000', '80000'));
        self::assertSame('within_limit', $result->details['band']);
        self::assertSame(100000, $result->details['headroom_minor']);
    }

    public function test_marks_excess_up_to_twenty_percent(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('80000', '90000'));
        self::assertSame('excess_up_to_20', $result->details['band']);
        self::assertSame(900000, $result->details['excess_minor']);
    }

    public function test_marks_excess_over_twenty_percent(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('81000', '100000'));
        self::assertSame('excess_over_20', $result->details['band']);
    }

    public function test_plus_projection_uses_user_parameters(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            currentAnnualRevenue: '80000',
            projectedAnnualRevenue: '100000',
            meEffectiveTaxRate: '6',
            monthlyAccountingCost: '500',
            monthlyOtherCost: '250',
            monthlyMeiCost: '0',
            annualGrowthRate: '5',
            projectionYears: 3,
            targetFixedCostBurden: '10',
        ));
        self::assertSame(900000, $result->details['plus']['annual_fixed_costs_minor']);
        self::assertSame(22500000, $result->details['plus']['migration_less_weight_revenue_minor']);
        self::assertCount(3, $result->details['plus']['projection']);
        self::assertSame(600000, $result->details['plus']['projection'][0]['estimated_taxes_minor']);
        self::assertSame(11000000, $result->details['plus']['projection'][1]['mei_limit_minor']);
    }
}
