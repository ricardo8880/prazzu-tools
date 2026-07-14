<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Domain\Data\LaborTerminationResult;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use DateTimeImmutable;

final readonly class CalculateLaborTermination
{
    public function __construct(private LaborTerminationCalculator $calculator) {}

    /** @param array<string, mixed> $input */
    public function execute(array $input): LaborTerminationResult
    {
        return $this->calculator->calculate(
            monthlySalary: Money::fromDecimal((string) $input['monthly_salary']),
            admissionDate: new DateTimeImmutable((string) $input['admission_date']),
            terminationDate: new DateTimeImmutable((string) $input['termination_date']),
            terminationType: TerminationType::from((string) $input['termination_type']),
            contractType: (string) $input['contract_type'],
            noticeType: NoticeType::from((string) $input['notice_type']),
            daysWorkedInMonth: (int) $input['days_worked_in_month'],
            overdueVacationPeriods: (int) ($input['overdue_vacation_periods'] ?? ((bool) ($input['has_overdue_vacation'] ?? false) ? 1 : 0)),
            doubleVacationPeriods: (int) ($input['double_vacation_periods'] ?? 0),
            fgtsBalance: Money::fromDecimal((string) ($input['fgts_balance'] ?? '0')),
            domesticIndemnityReserveBalance: Money::fromDecimal((string) ($input['domestic_indemnity_reserve_balance'] ?? '0')),
            otherDiscounts: Money::fromDecimal((string) ($input['other_discounts'] ?? '0')),
            dependents: (int) ($input['dependents'] ?? 0),
            commissionAverage: Money::fromDecimal((string) ($input['commission_average'] ?? '0')),
            overtimeAverage: Money::fromDecimal((string) ($input['overtime_average'] ?? '0')),
            recurringAdditions: Money::fromDecimal((string) ($input['recurring_additions'] ?? '0')),
            contractEndDate: filled($input['contract_end_date'] ?? null) ? new DateTimeImmutable((string) $input['contract_end_date']) : null,
            earlyTerminationInitiative: (string) ($input['early_termination_initiative'] ?? ''),
            article480Discount: Money::fromDecimal((string) ($input['article_480_discount'] ?? '0')),
            extraordinaryIndemnities: Money::fromDecimal((string) ($input['extraordinary_indemnities'] ?? '0')),
        );
    }
}
