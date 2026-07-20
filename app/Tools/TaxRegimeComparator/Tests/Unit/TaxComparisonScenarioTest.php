<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TaxComparisonScenarioTest extends TestCase
{
    public function test_it_accepts_a_complete_scenario_and_projects_annual_values(): void
    {
        $scenario = $this->scenario();

        self::assertSame(60_000_000, $scenario->annualRevenueProjection()->minorAmount());
        self::assertSame(14_400_000, $scenario->annualOperatingCostsProjection()->minorAmount());
        self::assertSame(9_600_000, $scenario->annualDeductibleExpensesProjection()->minorAmount());
    }

    public function test_it_rejects_zero_monthly_revenue(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O faturamento mensal deve ser maior que zero.');

        $this->scenario(monthlyRevenue: Money::zero());
    }

    public function test_it_rejects_negative_costs(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Os custos operacionais mensais não podem ser negativos.');

        $this->scenario(monthlyOperatingCosts: Money::fromDecimal('-0.01'));
    }

    public function test_it_rejects_invalid_state_abbreviation(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('O estado deve ser informado pela sigla em duas letras maiúsculas.');

        $this->scenario(state: 'Sp');
    }

    public function test_it_requires_state_when_municipality_is_informed(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Informe o estado quando o município for preenchido.');

        $this->scenario(state: null, municipality: 'São Paulo');
    }

    private function scenario(
        ?Money $monthlyRevenue = null,
        ?Money $payrollLastTwelveMonths = null,
        ?Money $monthlyOperatingCosts = null,
        ?string $state = 'SP',
        ?string $municipality = 'São Paulo',
    ): TaxComparisonScenario {
        return new TaxComparisonScenario(
            referenceDate: new DateTimeImmutable('2026-07-20'),
            businessActivity: BusinessActivity::AccountingServices,
            monthlyRevenue: $monthlyRevenue ?? Money::fromDecimal('50000.00'),
            revenueLastTwelveMonths: Money::fromDecimal('600000.00'),
            payrollLastTwelveMonths: $payrollLastTwelveMonths ?? Money::fromDecimal('180000.00'),
            monthlyOperatingCosts: $monthlyOperatingCosts ?? Money::fromDecimal('12000.00'),
            monthlyDeductibleExpenses: Money::fromDecimal('8000.00'),
            state: $state,
            municipality: $municipality,
        );
    }
}
