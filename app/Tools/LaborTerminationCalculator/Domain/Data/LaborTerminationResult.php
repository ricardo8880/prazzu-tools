<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use DateTimeImmutable;

final readonly class LaborTerminationResult
{
    /** @param array<int, string> $warnings */
    public function __construct(
        public Money $monthlySalary, public Money $salaryBase, public Money $commissionAverage, public Money $overtimeAverage,
        public Money $recurringAdditions, public Money $dailySalary, public Money $salaryBalance,
        public Money $overdueVacation, public Money $overdueVacationThird, public Money $proportionalVacation,
        public Money $proportionalVacationThird, public Money $proportionalThirteenthSalary, public Money $noticePay,
        public Money $article479Indemnity, public Money $extraordinaryIndemnities, public Money $noticeDiscount,
        public Money $article480Discount, public Money $grossTotal, public Money $inssSalary, public Money $inssThirteenth,
        public Money $irrfSalary, public Money $irrfThirteenth, public Money $otherDiscounts, public Money $totalDiscounts,
        public Money $netTotal, public Money $fgtsBalance, public Money $domesticIndemnityReserveBalance, public Money $domesticCompensatoryDeposit, public Money $fgtsTerminationDeposit, public Money $fgtsPenalty,
        public Money $estimatedFgtsAvailable, public int $fgtsWithdrawalPercentage, public int $daysWorkedInMonth,
        public int $proportionalVacationMonths, public int $proportionalThirteenthMonths, public int $overdueVacationPeriods,
        public int $doubleVacationPeriods, public int $noticeDays, public int $remainingContractDays, public int $dependents,
        public DateTimeImmutable $projectedTerminationDate, public ?DateTimeImmutable $contractEndDate,
        public TerminationType $terminationType, public NoticeType $noticeType, public string $contractType, public string $earlyTerminationInitiative,
        public array $warnings, public string $ruleVersion, public string $taxTableVersion,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $money = fn (Money $value): string => $value->formatPtBr();

        return [
            'monthly_salary' => $money($this->monthlySalary), 'salary_base' => $money($this->salaryBase),
            'commission_average' => $money($this->commissionAverage), 'overtime_average' => $money($this->overtimeAverage),
            'recurring_additions' => $money($this->recurringAdditions), 'daily_salary' => $money($this->dailySalary),
            'salary_balance' => $money($this->salaryBalance), 'overdue_vacation' => $money($this->overdueVacation),
            'overdue_vacation_third' => $money($this->overdueVacationThird), 'proportional_vacation' => $money($this->proportionalVacation),
            'proportional_vacation_third' => $money($this->proportionalVacationThird), 'proportional_thirteenth_salary' => $money($this->proportionalThirteenthSalary),
            'notice_pay' => $money($this->noticePay), 'article_479_indemnity' => $money($this->article479Indemnity),
            'extraordinary_indemnities' => $money($this->extraordinaryIndemnities), 'notice_discount' => $money($this->noticeDiscount),
            'article_480_discount' => $money($this->article480Discount), 'gross_total' => $money($this->grossTotal),
            'inss_salary' => $money($this->inssSalary), 'inss_thirteenth' => $money($this->inssThirteenth),
            'irrf_salary' => $money($this->irrfSalary), 'irrf_thirteenth' => $money($this->irrfThirteenth),
            'other_discounts' => $money($this->otherDiscounts), 'total_discounts' => $money($this->totalDiscounts), 'net_total' => $money($this->netTotal),
            'fgts_balance' => $money($this->fgtsBalance), 'domestic_indemnity_reserve_balance' => $money($this->domesticIndemnityReserveBalance),
            'domestic_compensatory_deposit' => $money($this->domesticCompensatoryDeposit), 'fgts_termination_deposit' => $money($this->fgtsTerminationDeposit),
            'fgts_penalty' => $money($this->fgtsPenalty), 'estimated_fgts_available' => $money($this->estimatedFgtsAvailable),
            'fgts_withdrawal_percentage' => $this->fgtsWithdrawalPercentage, 'days_worked_in_month' => $this->daysWorkedInMonth,
            'proportional_vacation_months' => $this->proportionalVacationMonths, 'proportional_thirteenth_months' => $this->proportionalThirteenthMonths,
            'overdue_vacation_periods' => $this->overdueVacationPeriods, 'double_vacation_periods' => $this->doubleVacationPeriods,
            'notice_days' => $this->noticeDays, 'remaining_contract_days' => $this->remainingContractDays, 'dependents' => $this->dependents,
            'projected_termination_date' => $this->projectedTerminationDate->format('d/m/Y'),
            'contract_end_date' => $this->contractEndDate?->format('d/m/Y'),
            'termination_type' => $this->terminationType->value, 'termination_type_label' => $this->terminationType->label(),
            'contract_type' => $this->contractType, 'is_domestic' => $this->contractType === 'domestic',
            'notice_type' => $this->noticeType->value, 'notice_type_label' => $this->noticeType->label(),
            'early_termination_initiative' => $this->earlyTerminationInitiative,
            'early_termination_initiative_label' => match ($this->earlyTerminationInitiative) { 'employer' => 'Empregador', 'employee' => 'Empregado', default => 'Não se aplica' },
            'warnings' => $this->warnings, 'rule_version' => $this->ruleVersion, 'tax_table_version' => $this->taxTableVersion,
        ];
    }
}
