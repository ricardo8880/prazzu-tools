<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\IncomeStatementGenerator\Application\Data\CalculationInput;
use App\Tools\IncomeStatementGenerator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_generates_statement(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('Ana', '000', 'Empresa', 2025, Money::fromDecimal('50000'), Money::fromDecimal('5000'), Money::fromDecimal('3000'), Money::fromDecimal('2000')));
        self::assertSame('R$ 40.000,00', $r->summary[2]->value);
        self::assertSame(2025, $r->details['document']['year']);
    }
}
