<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Unit;

use App\Tools\TaxInstallmentCalculator\Application\Data\CalculationInput;
use App\Tools\TaxInstallmentCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_sac_calculates_average_installment_charges_and_final_cost(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput([[
            'name' => 'Principal', 'debt' => '12.000,00', 'entry' => '0', 'installments' => 12, 'monthly_charge' => '1',
        ]]));

        self::assertSame('R$ 1.065,00', $result->summary[1]->value);
        self::assertSame('R$ 780,00', $result->summary[2]->value);
        self::assertSame('R$ 12.780,00', $result->summary[3]->value);
        self::assertSame(112000, $result->details['scenarios'][0]['first_installment_minor']);
        self::assertSame(101000, $result->details['scenarios'][0]['last_installment_minor']);
        self::assertSame(0, $result->details['scenarios'][0]['schedule'][11]['closing_balance_minor']);
    }

    public function test_entry_reduces_financed_balance_and_multiple_scenarios_are_compared(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput([
            ['name' => 'Com entrada', 'debt' => '10.000,00', 'entry' => '2.000,00', 'installments' => 8, 'monthly_charge' => '1'],
            ['name' => 'Prazo maior', 'debt' => '10.000,00', 'entry' => '0', 'installments' => 10, 'monthly_charge' => '1'],
        ]));

        self::assertCount(2, $result->details['comparison']);
        self::assertSame(800000, $result->details['scenarios'][0]['financed_minor']);
        self::assertCount(8, $result->details['scenarios'][0]['schedule']);
    }
}
