<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Tests\Unit;

use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data\CalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Services\Calculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_first_quarter_commerce_calculates_irpj_additional_and_csll(): void
    {
        $result = (new Calculator)->calculate($this->input(quarter: 1, commerce: '1000000.00'));

        self::assertSame('R$ 80.000,00', $result->summary[0]->value);
        self::assertSame('R$ 14.000,00', $result->summary[1]->value);
        self::assertSame('R$ 120.000,00', $result->summary[2]->value);
        self::assertSame('R$ 10.800,00', $result->summary[3]->value);
        self::assertSame('R$ 24.800,00', $result->summary[4]->value);
        self::assertSame(200_000, $result->details['irpj_additional_minor']);
        self::assertCount(1, $result->calculationMemory?->normativeRules ?? []);
    }

    public function test_2026_increased_presumption_applies_to_irpj_excess_in_first_quarter(): void
    {
        $result = (new Calculator)->calculate($this->input(quarter: 1, commerce: '2000000.00'));

        self::assertSame(125_000_000, $result->details['irpj_normal_allowance_minor']);
        self::assertSame(16_600_000, $result->details['irpj_presumed_base_minor']);
        self::assertSame(24_000_000, $result->details['csll_presumed_base_minor']);
    }

    public function test_csll_increased_presumption_starts_in_second_quarter(): void
    {
        $result = (new Calculator)->calculate($this->input(quarter: 2, commerce: '2000000.00', priorIrpj: '2000000.00'));

        self::assertSame(50_000_000, $result->details['irpj_normal_allowance_minor']);
        self::assertSame(125_000_000, $result->details['csll_normal_allowance_minor']);
        self::assertSame(24_900_000, $result->details['csll_presumed_base_minor']);
    }

    public function test_multiple_activities_receive_proportional_normal_allowance(): void
    {
        $result = (new Calculator)->calculate($this->input(quarter: 1, commerce: '1000000.00', services: '1000000.00'));

        self::assertSame(62_500_000, $result->details['activities']['commerce_industry']['irpj_normal_revenue_minor']);
        self::assertSame(62_500_000, $result->details['activities']['services_general']['irpj_normal_revenue_minor']);
    }

    public function test_zero_revenue_is_rejected_in_domain(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Calculator)->calculate($this->input(quarter: 1));
    }

    private function input(
        int $quarter,
        string $commerce = '0',
        string $fuel = '0',
        string $passenger = '0',
        string $services = '0',
        string $additions = '0',
        string $priorIrpj = '0',
        string $priorCsll = '0',
        string $irpjCredits = '0',
        string $csllCredits = '0',
    ): CalculationInput {
        return new CalculationInput($quarter, $commerce, $fuel, $passenger, $services, $additions, $priorIrpj, $priorCsll, $irpjCredits, $csllCredits);
    }
}
