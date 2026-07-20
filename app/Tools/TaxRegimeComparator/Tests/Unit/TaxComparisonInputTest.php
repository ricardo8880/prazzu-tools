<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Application\Data\TaxComparisonInput;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TaxComparisonInputTest extends TestCase
{
    public function test_it_serializes_money_as_minor_units_and_preserves_reference_date(): void
    {
        $input = $this->input();

        self::assertSame('2026-07-20', $input->toArray()['reference_date']);
        self::assertSame('accounting_services', $input->toArray()['business_activity']);
        self::assertSame(5_000_000, $input->toArray()['monthly_revenue_minor']);
        self::assertSame(60_000_000, $input->toArray()['revenue_last_twelve_months_minor']);
    }

    public function test_it_creates_a_validated_domain_scenario(): void
    {
        $scenario = $this->input()->toScenario();

        self::assertSame(BusinessActivity::AccountingServices, $scenario->businessActivity);
        self::assertSame(60_000_000, $scenario->annualRevenueProjection()->minorAmount());
        self::assertSame(14_400_000, $scenario->annualOperatingCostsProjection()->minorAmount());
        self::assertSame(9_600_000, $scenario->annualDeductibleExpensesProjection()->minorAmount());
    }

    private function input(): TaxComparisonInput
    {
        return new TaxComparisonInput(
            referenceDate: new DateTimeImmutable('2026-07-20'),
            businessActivity: BusinessActivity::AccountingServices,
            monthlyRevenue: Money::fromDecimal('50000.00'),
            revenueLastTwelveMonths: Money::fromDecimal('600000.00'),
            payrollLastTwelveMonths: Money::fromDecimal('180000.00'),
            monthlyOperatingCosts: Money::fromDecimal('12000.00'),
            monthlyDeductibleExpenses: Money::fromDecimal('8000.00'),
            state: 'SP',
            municipality: 'São Paulo',
        );
    }
}
