<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Tests\Unit;

use App\Tools\OvertimeCalculator\Application\Data\CalculationInput;
use App\Tools\OvertimeCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_overtime_50_percent(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('2026-07', '2200.00', 220, '10'));
        self::assertSame('R$ 10,00', $r->summary[0]->value);
        self::assertSame('R$ 150,00', $r->summary[1]->value);
        self::assertSame('R$ 150,00', $r->summary[4]->value);
    }

    public function test_night_reduction_and_dsr(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('2026-07', '2200.00', 220, '10', '0', '0', '50', '7', '0', '50', 22, 4, true, true));
        self::assertGreaterThan(0, $r->details['night_minor']);
        self::assertGreaterThan(0, $r->details['dsr_minor']);
        self::assertGreaterThan(0, $r->details['thirteenth_minor']);
        self::assertCount(1,$r->calculationMemory->normativeRules);
    }
}
