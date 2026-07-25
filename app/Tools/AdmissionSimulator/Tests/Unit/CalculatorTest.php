<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Tests\Unit;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\AdmissionSimulator\Application\Data\CalculationInput;
use App\Tools\AdmissionSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_first_month_and_checklist(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput(Money::fromDecimal('3000'), Money::fromDecimal('500'), Percentage::fromString('40'), Money::fromDecimal('200'), Money::fromDecimal('300'), Money::fromDecimal('1000'), Money::zero()));
        self::assertSame('R$ 6.200,00', $r->summary[0]->value);
        self::assertSame('R$ 4.700,00', $r->summary[1]->value);
        self::assertCount(10, $r->details['checklist']);
    }
}
