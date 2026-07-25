<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\WorkingCapitalCalculator\Application\Data\CalculationInput;
use App\Tools\WorkingCapitalCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_returns_the_standardized_result_contract(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            Money::fromDecimal('10000'), Money::fromDecimal('50000'), Money::fromDecimal('30000'),
            Money::fromDecimal('5000'), Money::fromDecimal('25000'), Money::fromDecimal('5000'),
            Money::fromDecimal('10000'), Money::fromDecimal('5000'),
        ));

        self::assertSame('capital-de-giro', $result->toolSlug);
        self::assertSame('1.0.0', $result->schemaVersion);
        self::assertSame('required_capital', $result->summary[0]->key);
        self::assertSame('R$ 55.000,00', $result->summary[0]->value);
        self::assertSame('R$ 50.000,00', $result->summary[2]->value);
        self::assertSame('R$ 5.000,00', $result->summary[3]->value);
    }
}
