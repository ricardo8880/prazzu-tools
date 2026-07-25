<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\PayslipGenerator\Application\Data\CalculationInput;
use App\Tools\PayslipGenerator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_generates_totals(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('Ana', '000', 'Empresa', '2026-07', Money::fromDecimal('3000'), Money::fromDecimal('200'), Money::fromDecimal('300'), Money::fromDecimal('100'), Money::fromDecimal('50')));
        self::assertSame('R$ 3.200,00', $r->summary[0]->value);
        self::assertSame('R$ 2.750,00', $r->summary[2]->value);
    }
}
