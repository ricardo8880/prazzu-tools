<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Application\Actions;

final readonly class PlanVacations
{
    public function __construct(private CalculateTool $calculator) {}

    /** @param list<array<string,mixed>> $employees @return list<array<string,mixed>> */
    public function execute(array $employees): array
    {
        $rows = [];
        foreach ($employees as $employee) {
            $input = [
                'monthly_salary' => $employee['monthly_salary'],
                'acquisition_start_date' => $employee['acquisition_start_date'],
                'vacation_start_date' => $employee['vacation_start_date'],
                'unjustified_absences' => $employee['unjustified_absences'] ?? 0,
                'convert_one_third_to_cash' => (bool) ($employee['convert_one_third_to_cash'] ?? false),
                'commission_average' => '0', 'overtime_average' => '0',
                'recurring_additions' => '0', 'other_deductions' => '0',
            ];
            $rows[] = ['name' => $employee['name'], 'input' => $input, 'result' => $this->calculator->execute($input)->toArray()];
        }

        return $rows;
    }
}
