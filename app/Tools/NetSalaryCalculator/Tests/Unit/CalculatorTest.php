<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Unit;

use App\Tools\NetSalaryCalculator\Application\Data\CalculationInput;
use App\Tools\NetSalaryCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_calculates_regular_monthly_salary_with_2026_inss_and_irrf_reduction(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-01', '5000.00'));

        self::assertSame('calculadora-salario-liquido', $result->toolSlug);
        self::assertSame('R$ 501,51', $result->summary[1]->value);
        self::assertSame('R$ 0,00', $result->summary[2]->value);
        self::assertSame('R$ 4.498,49', $result->summary[4]->value);
        self::assertSame('simplified', $result->details['irrf_deduction_method']);
        self::assertNotNull($result->calculationMemory);
        self::assertCount(2, $result->calculationMemory->normativeRules);
    }

    public function test_it_caps_social_security_and_applies_irrf_above_reduction_range(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('2026-01', '10000.00'));

        self::assertSame(847555, $result->details['social_security_base_minor']);
        self::assertSame('R$ 988,09', $result->summary[1]->value);
        self::assertSame('R$ 1.569,55', $result->summary[2]->value);
        self::assertSame('R$ 7.442,36', $result->summary[4]->value);
        self::assertSame(0, $result->details['irrf_reduction_minor']);
    }

    public function test_plus_inputs_affect_earnings_and_user_discounts_without_changing_core_formula(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            competence: '2026-01',
            baseSalary: '5000.00',
            taxableAdditionalEarnings: '1000.00',
            nonTaxableEarnings: '200.00',
            dependents: 1,
            judicialPension: '100.00',
            transportDiscount: '150.00',
            mealDiscount: '50.00',
            healthPlanDiscount: '100.00',
            otherDiscounts: '25.00',
        ));

        self::assertSame(620000, $result->details['total_earnings_minor']);
        self::assertSame(42500, $result->details['user_discounts_minor']);
        self::assertLessThan(620000, $result->details['net_minor']);
        self::assertNotEmpty($result->calculationMemory->steps);
    }
}
