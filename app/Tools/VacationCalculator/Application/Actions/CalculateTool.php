<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use App\Tools\VacationCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string,mixed> $data */
    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            hourlyRate: (string) $data['hourly_rate'],
            accruedPtoHours: (string) $data['accrued_pto_hours'],
            plannedPtoHours: (string) $data['planned_pto_hours'],
            accrualHoursPerPayPeriod: (string) ($data['accrual_hours_per_pay_period'] ?? '0'),
            payPeriodsBeforeLeave: (int) ($data['pay_periods_before_leave'] ?? 0),
            payoutPercent: (string) ($data['payout_percent'] ?? '100'),
        ));
    }
}
