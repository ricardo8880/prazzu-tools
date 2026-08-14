<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Tests\Unit;

use App\Tools\EcadRoyaltySimulator\Application\Data\CalculationInput;
use App\Tools\EcadRoyaltySimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_calculates_direct_uda_without_float(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('uda', '107,31', udaQuantity: '3'));
        self::assertSame('R$ 321,93', $result->summary[0]->value);
    }

    public function test_calculates_uda_per_square_meter(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('uda_per_sqm', '107,31', areaSquareMeters: '100', udaPerSquareMeter: '0,012'));
        self::assertSame('R$ 128,77', $result->summary[0]->value);
    }

    public function test_calculates_percentage_and_projection(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('percentage', '107,31', referenceAmount: '10000', percentageRate: '2,5', periods: 12));
        self::assertSame('R$ 250,00', $result->summary[0]->value);
        self::assertSame('R$ 3.000,00', $result->summary[2]->value);
    }
}
