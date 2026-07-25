<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\LateDasCalculator\Application\Data\CalculationInput;
use App\Tools\LateDasCalculator\Domain\Services\Calculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_caps_fine_and_adds_selic_plus_one_percent(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('1000'), new DateTimeImmutable('2025-01-01'), new DateTimeImmutable('2025-04-11'), Percentage::fromString('2.5')));
        self::assertSame('R$ 1.235,00', $r->summary[0]->value);
        self::assertSame('R$ 200,00', $r->summary[1]->value);
        self::assertSame('R$ 35,00', $r->summary[2]->value);
    }
}
