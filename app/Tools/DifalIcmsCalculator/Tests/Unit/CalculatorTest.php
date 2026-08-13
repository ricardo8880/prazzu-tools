<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Tests\Unit;

use App\Tools\DifalIcmsCalculator\Application\Data\CalculationInput;
use App\Tools\DifalIcmsCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_single_base_sp_to_ba_uses_seven_percent(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('2026-07', '1000.00', 'SP', 'BA', false, null, '18', '2'));
        self::assertSame('7%', $r->summary[0]->value);
        self::assertSame('R$ 110,00', $r->summary[2]->value);
        self::assertSame('R$ 20,00', $r->summary[3]->value);
        self::assertSame('R$ 130,00', $r->summary[4]->value);
        self::assertCount(1, $r->calculationMemory->normativeRules);
    }

    public function test_imported_rule_uses_four_percent(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('2026-07', '1000.00', 'SP', 'MG', true, null, '18'));
        self::assertSame('4%', $r->summary[0]->value);
        self::assertSame('R$ 140,00', $r->summary[2]->value);
    }

    public function test_double_base_is_available_as_plus_scenario(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('2026-07', '1000.00', 'SP', 'MG', false, null, '18', '0', 'double_base'));
        self::assertGreaterThan(100000, $r->details['destination_base_minor']);
        self::assertGreaterThan(6000,$r->details['difal_minor']);
    }
}
