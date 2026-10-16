<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Unit;

use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use App\Tools\TaxReformSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_sales_tax_is_added_to_tax_exclusive_price(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('100.00', '6.25', '1.50', '0.25', 2, false));

        self::assertSame('$200.00', $result->summary[0]->value);
        self::assertSame('8%', $result->summary[1]->value);
        self::assertSame('$16.00', $result->summary[2]->value);
        self::assertSame('$216.00', $result->summary[3]->value);
    }

    public function test_tax_can_be_extracted_from_tax_inclusive_total(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('108.00', '6', '2', '0', 1, true));

        self::assertSame('$100.00', $result->summary[0]->value);
        self::assertSame('$8.00', $result->summary[2]->value);
        self::assertSame('$108.00', $result->summary[3]->value);
    }
}
