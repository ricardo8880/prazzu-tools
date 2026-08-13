<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Tests\Unit;

use App\Tools\AssetDepreciationCalculator\Application\Data\CalculationInput;
use App\Tools\AssetDepreciationCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_linear_method_calculates_monthly_annual_and_book_value(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput([
            ['name' => 'Notebook', 'value' => '12.000,00', 'useful_life_years' => 5, 'method' => 'linear'],
        ]));

        self::assertSame('R$ 200,00', $result->summary[1]->value);
        self::assertSame('R$ 2.400,00', $result->summary[2]->value);
        self::assertSame('R$ 9.600,00', $result->summary[3]->value);
        self::assertSame(0, $result->details['assets'][0]['schedule'][4]['book_value_minor']);
    }

    public function test_declining_balance_accelerates_first_year_and_finishes_at_zero(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput([
            ['name' => 'Máquina', 'value' => '10.000,00', 'useful_life_years' => 5, 'method' => 'declining_balance'],
        ]));

        self::assertSame(400000, $result->details['assets'][0]['first_year_depreciation_minor']);
        self::assertSame(0, $result->details['assets'][0]['schedule'][4]['book_value_minor']);
    }

    public function test_sum_of_years_digits_and_multiple_assets_create_portfolio_projection(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput([
            ['name' => 'Veículo', 'value' => '15.000,00', 'useful_life_years' => 5, 'method' => 'sum_of_years_digits'],
            ['name' => 'Notebook', 'value' => '12.000,00', 'useful_life_years' => 3, 'method' => 'linear'],
        ]));

        self::assertCount(2, $result->details['assets']);
        self::assertSame(500000, $result->details['assets'][0]['first_year_depreciation_minor']);
        self::assertSame(2700000, $result->details['portfolio_cost_minor']);
        self::assertCount(5, $result->details['portfolio']);
        self::assertSame(0, $result->details['portfolio'][4]['book_value_minor']);
    }
}
