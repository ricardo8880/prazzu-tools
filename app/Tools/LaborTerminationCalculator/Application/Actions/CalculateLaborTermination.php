<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\LaborTerminationCalculator\Domain\Calculators\LaborTerminationCalculator;
use App\Tools\LaborTerminationCalculator\Domain\Data\LaborTerminationResult;
use DateTimeImmutable;

final readonly class CalculateLaborTermination
{
    public function __construct(private LaborTerminationCalculator $calculator) {}

    /** @param array<string,mixed> $input */
    public function execute(array $input): LaborTerminationResult
    {
        return $this->calculator->calculate(
            terminationDate: new DateTimeImmutable((string) $input['termination_date']),
            separationType: (string) $input['separation_type'],
            hourlyRate: Money::fromDecimal((string) $input['hourly_rate']),
            regularHoursDue: (string) $input['regular_hours_due'],
            unusedPtoHours: (string) ($input['unused_pto_hours'] ?? '0'),
            ptoPayoutRate: Percentage::fromString((string) ($input['pto_payout_percent'] ?? '100')),
            weeklyBasePay: Money::fromDecimal((string) ($input['weekly_base_pay'] ?? '0')),
            severanceWeeks: (string) ($input['severance_weeks'] ?? '0'),
            bonusCommissionDue: Money::fromDecimal((string) ($input['bonus_commission_due'] ?? '0')),
            otherPay: Money::fromDecimal((string) ($input['other_pay'] ?? '0')),
            federalWithholding: Money::fromDecimal((string) ($input['federal_withholding'] ?? '0')),
            stateLocalWithholding: Money::fromDecimal((string) ($input['state_local_withholding'] ?? '0')),
            otherDeductions: Money::fromDecimal((string) ($input['other_deductions'] ?? '0')),
        );
    }
}
