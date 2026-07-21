<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use App\Tools\VacationCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            monthlySalary: (string) $data['monthly_salary'],
            acquisitionStartDate: (string) $data['acquisition_start_date'],
            vacationStartDate: (string) $data['vacation_start_date'],
            unjustifiedAbsences: (int) ($data['unjustified_absences'] ?? 0),
            convertOneThirdToCash: (bool) ($data['convert_one_third_to_cash'] ?? false),
            commissionAverage: (string) ($data['commission_average'] ?? '0'),
            overtimeAverage: (string) ($data['overtime_average'] ?? '0'),
            recurringAdditions: (string) ($data['recurring_additions'] ?? '0'),
            otherDeductions: (string) ($data['other_deductions'] ?? '0'),
        ));
    }
}
