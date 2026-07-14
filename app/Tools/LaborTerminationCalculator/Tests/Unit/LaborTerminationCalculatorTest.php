<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class LaborTerminationCalculatorTest extends TestCase
{
    public function test_dismissal_without_cause_adds_proportional_indemnified_notice_and_projects_contract(): void
    {
        $result = $this->calculate(TerminationType::DismissalWithoutCause, NoticeType::Indemnified);

        self::assertSame(36, $result->noticeDays);
        self::assertSame(360000, $result->noticePay->minorAmount());
        self::assertSame('2026-08-19', $result->projectedTerminationDate->format('Y-m-d'));
        self::assertSame(7, $result->proportionalVacationMonths);
        self::assertSame(8, $result->proportionalThirteenthMonths);
        self::assertSame(0, $result->noticeDiscount->minorAmount());
    }

    public function test_resignation_without_working_notice_applies_only_thirty_day_discount(): void
    {
        $result = $this->calculate(TerminationType::Resignation, NoticeType::NotWorked);

        self::assertSame(30, $result->noticeDays);
        self::assertSame(0, $result->noticePay->minorAmount());
        self::assertSame(300000, $result->noticeDiscount->minorAmount());
        self::assertSame('2026-07-14', $result->projectedTerminationDate->format('Y-m-d'));
    }

    public function test_dismissal_with_cause_excludes_proportional_vacation_thirteenth_and_notice(): void
    {
        $result = $this->calculate(TerminationType::DismissalWithCause, NoticeType::NotApplicable);

        self::assertSame(0, $result->proportionalVacationMonths);
        self::assertSame(0, $result->proportionalThirteenthMonths);
        self::assertSame(0, $result->noticeDays);
        self::assertSame(540000, $result->grossTotal->minorAmount());
    }

    public function test_mutual_agreement_pays_half_of_indemnified_notice(): void
    {
        $result = $this->calculate(TerminationType::MutualAgreement, NoticeType::Indemnified);

        self::assertSame(36, $result->noticeDays);
        self::assertSame(180000, $result->noticePay->minorAmount());
    }

    public function test_fixed_term_contract_rejects_common_notice(): void
    {
        $this->expectException(InvalidValue::class);

        (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'),
            admissionDate: new DateTimeImmutable('2024-01-10'),
            terminationDate: new DateTimeImmutable('2026-07-14'),
            terminationType: TerminationType::ContractEnd,
            contractType: 'fixed_term',
            noticeType: NoticeType::Worked,
            daysWorkedInMonth: 14,
            overdueVacationPeriods: 0,
            doubleVacationPeriods: 0,
            fgtsBalance: Money::fromDecimal('0,00'),
            domesticIndemnityReserveBalance: Money::fromDecimal('0,00'),
            otherDiscounts: Money::fromDecimal('0,00'),
            dependents: 0,
            commissionAverage: Money::fromDecimal('0,00'),
            overtimeAverage: Money::fromDecimal('0,00'),
            recurringAdditions: Money::fromDecimal('0,00'),
            contractEndDate: null,
            earlyTerminationInitiative: '',
            article480Discount: Money::fromDecimal('0,00'),
            extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );
    }

    public function test_variable_pay_and_double_vacation_use_integrated_salary_base(): void
    {
        $result = (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'), admissionDate: new DateTimeImmutable('2025-01-01'),
            terminationDate: new DateTimeImmutable('2026-07-14'), terminationType: TerminationType::DismissalWithoutCause,
            contractType: 'indefinite', noticeType: NoticeType::Worked, daysWorkedInMonth: 14, overdueVacationPeriods: 1,
            doubleVacationPeriods: 1, fgtsBalance: Money::fromDecimal('0,00'), domesticIndemnityReserveBalance: Money::fromDecimal('0,00'), otherDiscounts: Money::fromDecimal('0,00'), dependents: 0,
            commissionAverage: Money::fromDecimal('500,00'), overtimeAverage: Money::fromDecimal('300,00'), recurringAdditions: Money::fromDecimal('200,00'),
            contractEndDate: null, earlyTerminationInitiative: '', article480Discount: Money::fromDecimal('0,00'), extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );

        self::assertSame(400000, $result->salaryBase->minorAmount());
        self::assertSame(800000, $result->overdueVacation->minorAmount());
        self::assertSame(1, $result->doubleVacationPeriods);
    }

    public function test_employer_early_termination_calculates_article_479_half_remaining_remuneration(): void
    {
        $result = (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'), admissionDate: new DateTimeImmutable('2026-01-01'),
            terminationDate: new DateTimeImmutable('2026-07-01'), terminationType: TerminationType::EarlyContractEnd,
            contractType: 'experience', noticeType: NoticeType::NotApplicable, daysWorkedInMonth: 1, overdueVacationPeriods: 0,
            doubleVacationPeriods: 0, fgtsBalance: Money::fromDecimal('0,00'), domesticIndemnityReserveBalance: Money::fromDecimal('0,00'), otherDiscounts: Money::fromDecimal('0,00'), dependents: 0,
            commissionAverage: Money::fromDecimal('0,00'), overtimeAverage: Money::fromDecimal('0,00'), recurringAdditions: Money::fromDecimal('0,00'),
            contractEndDate: new DateTimeImmutable('2026-07-31'), earlyTerminationInitiative: 'employer', article480Discount: Money::fromDecimal('0,00'), extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );

        self::assertSame(30, $result->remainingContractDays);
        self::assertSame(150000, $result->article479Indemnity->minorAmount());
    }


    public function test_domestic_dismissal_uses_compensatory_reserve_instead_of_forty_percent_penalty(): void
    {
        $result = (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'), admissionDate: new DateTimeImmutable('2025-01-01'),
            terminationDate: new DateTimeImmutable('2026-07-14'), terminationType: TerminationType::DismissalWithoutCause,
            contractType: 'domestic', noticeType: NoticeType::Worked, daysWorkedInMonth: 14, overdueVacationPeriods: 0,
            doubleVacationPeriods: 0, fgtsBalance: Money::fromDecimal('10.000,00'),
            domesticIndemnityReserveBalance: Money::fromDecimal('4.000,00'), otherDiscounts: Money::fromDecimal('0,00'), dependents: 0,
            commissionAverage: Money::fromDecimal('0,00'), overtimeAverage: Money::fromDecimal('0,00'), recurringAdditions: Money::fromDecimal('0,00'),
            contractEndDate: null, earlyTerminationInitiative: '', article480Discount: Money::fromDecimal('0,00'), extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );

        self::assertTrue($result->contractType === 'domestic');
        self::assertGreaterThan(0, $result->domesticCompensatoryDeposit->minorAmount());
        self::assertSame(
            $result->domesticIndemnityReserveBalance->add($result->domesticCompensatoryDeposit)->minorAmount(),
            $result->fgtsPenalty->minorAmount(),
        );
    }

    public function test_domestic_resignation_does_not_release_compensatory_reserve_to_employee(): void
    {
        $result = (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'), admissionDate: new DateTimeImmutable('2025-01-01'),
            terminationDate: new DateTimeImmutable('2026-07-14'), terminationType: TerminationType::Resignation,
            contractType: 'domestic', noticeType: NoticeType::Worked, daysWorkedInMonth: 14, overdueVacationPeriods: 0,
            doubleVacationPeriods: 0, fgtsBalance: Money::fromDecimal('10.000,00'),
            domesticIndemnityReserveBalance: Money::fromDecimal('4.000,00'), otherDiscounts: Money::fromDecimal('0,00'), dependents: 0,
            commissionAverage: Money::fromDecimal('0,00'), overtimeAverage: Money::fromDecimal('0,00'), recurringAdditions: Money::fromDecimal('0,00'),
            contractEndDate: null, earlyTerminationInitiative: '', article480Discount: Money::fromDecimal('0,00'), extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );

        self::assertSame(0, $result->fgtsPenalty->minorAmount());
        self::assertSame(0, $result->estimatedFgtsAvailable->minorAmount());
    }

    private function calculate(TerminationType $terminationType, NoticeType $noticeType)
    {
        return (new LaborTerminationCalculator)->calculate(
            monthlySalary: Money::fromDecimal('3.000,00'),
            admissionDate: new DateTimeImmutable('2024-01-10'),
            terminationDate: new DateTimeImmutable('2026-07-14'),
            terminationType: $terminationType,
            contractType: 'indefinite',
            noticeType: $noticeType,
            daysWorkedInMonth: 14,
            overdueVacationPeriods: 1,
            doubleVacationPeriods: 0,
            fgtsBalance: Money::fromDecimal('10.000,00'),
            domesticIndemnityReserveBalance: Money::fromDecimal('0,00'),
            otherDiscounts: Money::fromDecimal('0,00'),
            dependents: 0,
            commissionAverage: Money::fromDecimal('0,00'),
            overtimeAverage: Money::fromDecimal('0,00'),
            recurringAdditions: Money::fromDecimal('0,00'),
            contractEndDate: null,
            earlyTerminationInitiative: '',
            article480Discount: Money::fromDecimal('0,00'),
            extraordinaryIndemnities: Money::fromDecimal('0,00'),
        );
    }
}
