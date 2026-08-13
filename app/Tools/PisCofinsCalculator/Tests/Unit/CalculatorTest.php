<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Tests\Unit;

use App\Tools\PisCofinsCalculator\Application\Data\CalculationInput;
use App\Tools\PisCofinsCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_cumulative_general_rates(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-08', 'cumulative', false, '10000', '0', '0', '0'));
        self::assertSame('R$ 65,00', $result->summary[0]->value);
        self::assertSame('R$ 300,00', $result->summary[1]->value);
        self::assertSame('R$ 365,00', $result->summary[2]->value);
    }

    public function test_non_cumulative_discounts_eligible_credits(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-08', 'non_cumulative', true, '10000', '4000', '0', '0'));
        self::assertSame('R$ 99,00', $result->summary[0]->value);
        self::assertSame('R$ 456,00', $result->summary[1]->value);
        self::assertSame(36500, $result->details['comparison']['cumulative']['total_payable_minor']);
    }

    public function test_additional_operations_are_aggregated(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-08', 'cumulative', false, '10000', '0', '0', '0', [
            ['description' => 'Operação 2', 'revenue' => '5000', 'credit_base' => '0'],
        ]));
        self::assertSame(1500000, $result->details['revenue_minor']);
        self::assertSame('R$ 547,50',$result->summary[2]->value);
    }
}
