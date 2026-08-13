<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Unit;

use App\Tools\ProfitDistributionBalanceSimulator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionBalanceSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_compares_with_and_without_balance(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('240.000,00', '72.000,00', '32', '14.400,00'));
        self::assertSame(7200000, $r->details['with_balance_minor']);
        self::assertSame(6240000, $r->details['without_balance_minor']);
        self::assertSame(960000, $r->details['difference_minor']);
    }

    public function test_builds_planning_rows(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('120.000,00', '36.000,00', '32', '7.200,00', '0', '3.000,00', '0', 12));
        self::assertCount(12, $r->details['planning']['rows']);
        self::assertSame(3600000, $r->details['planning']['total_pro_labore_minor']);
    }
}
