<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\VacationCalculator\Domain\Data\VacationInput;
use App\Tools\VacationCalculator\Domain\Services\VacationCalculator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class VacationCalculatorTest extends TestCase
{
    public function test_full_vacation_adds_the_constitutional_third(): void
    {
        $result = $this->calculate();

        self::assertSame(30, $result->entitledDays);
        self::assertSame(30, $result->leaveDays);
        self::assertSame(0, $result->cashAllowanceDays);
        self::assertSame(300000, $result->vacationRemuneration->minorAmount());
        self::assertSame(100000, $result->vacationThird->minorAmount());
        self::assertSame(400000, $result->grossTotal->minorAmount());
    }

    public function test_cash_allowance_converts_one_third_without_changing_total_gross_remuneration(): void
    {
        $result = $this->calculate(convertOneThirdToCash: true);

        self::assertSame(20, $result->leaveDays);
        self::assertSame(10, $result->cashAllowanceDays);
        self::assertSame(200000, $result->vacationRemuneration->minorAmount());
        self::assertSame(100000, $result->cashAllowance->minorAmount());
        self::assertSame(400000, $result->grossTotal->minorAmount());
    }

    public function test_absences_reduce_days_and_remuneration_by_the_statutory_band(): void
    {
        $result = $this->calculate(unjustifiedAbsences: 6);

        self::assertSame(24, $result->entitledDays);
        self::assertSame(240000, $result->vacationRemuneration->minorAmount());
        self::assertSame(80000, $result->vacationThird->minorAmount());
        self::assertSame(320000, $result->grossTotal->minorAmount());
    }

    public function test_variable_pay_is_integrated_into_the_remuneration_base(): void
    {
        $result = $this->calculate(
            commissionAverage: '500,00',
            overtimeAverage: '300,00',
            recurringAdditions: '200,00',
        );

        self::assertSame(400000, $result->remunerationBase->minorAmount());
        self::assertSame(533333, $result->grossTotal->minorAmount());
        self::assertNotEmpty($result->warnings);
    }

    public function test_it_calculates_acquisition_concession_and_payment_dates(): void
    {
        $result = $this->calculate();

        self::assertSame('2025-12-31', $result->acquisitionEndDate->format('Y-m-d'));
        self::assertSame('2026-12-31', $result->concessionDeadline->format('Y-m-d'));
        self::assertSame('2026-06-29', $result->paymentDeadline->format('Y-m-d'));
        self::assertFalse($result->concessionPeriodOverdue);
    }

    public function test_more_than_thirty_two_absences_removes_entitlement(): void
    {
        $result = $this->calculate(unjustifiedAbsences: 33, convertOneThirdToCash: true);

        self::assertSame(0, $result->entitledDays);
        self::assertSame(0, $result->grossTotal->minorAmount());
        self::assertNotEmpty($result->warnings);
    }

    public function test_zero_salary_is_rejected(): void
    {
        $this->expectException(InvalidValue::class);
        $this->calculate(monthlySalary: '0,00');
    }

    private function calculate(
        string $monthlySalary = '3.000,00',
        int $unjustifiedAbsences = 0,
        bool $convertOneThirdToCash = false,
        string $commissionAverage = '0,00',
        string $overtimeAverage = '0,00',
        string $recurringAdditions = '0,00',
        string $otherDeductions = '0,00',
    ) {
        return (new VacationCalculator)->calculate(new VacationInput(
            monthlySalary: Money::fromDecimal($monthlySalary),
            acquisitionStartDate: new DateTimeImmutable('2025-01-01'),
            vacationStartDate: new DateTimeImmutable('2026-07-01'),
            unjustifiedAbsences: $unjustifiedAbsences,
            convertOneThirdToCash: $convertOneThirdToCash,
            commissionAverage: Money::fromDecimal($commissionAverage),
            overtimeAverage: Money::fromDecimal($overtimeAverage),
            recurringAdditions: Money::fromDecimal($recurringAdditions),
            otherDeductions: Money::fromDecimal($otherDeductions),
        ));
    }
}
