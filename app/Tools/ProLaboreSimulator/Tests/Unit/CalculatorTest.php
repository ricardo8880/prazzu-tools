<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Tests\Unit;

use App\Tools\ProLaboreSimulator\Application\Data\CalculationInput;
use App\Tools\ProLaboreSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_calculates_pro_labore_without_profit_distribution_dependency(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-01', 'presumed_profit', '5000.00'));

        self::assertSame('simulador-pro-labore-ideal', $result->toolSlug);
        self::assertArrayHasKey('normative_rules', $result->details);
        self::assertGreaterThan(0, $result->details['net_minor']);
    }
}
