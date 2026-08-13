<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Tests\Unit;

use App\Tools\TurnoverCalculator\Application\Data\CalculationInput;
use App\Tools\TurnoverCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_calculates_turnover_without_floating_domain_input(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(10, 6, 80));

        self::assertSame('10,00%', $result->summary[0]->value);
        self::assertSame(16, $result->summary[1]->value);
    }
}
