<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\VacationCalculator\Domain\Data\VacationInput;
use DateTimeImmutable;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $monthlySalary,
        public string $acquisitionStartDate,
        public string $vacationStartDate,
        public int $unjustifiedAbsences = 0,
        public bool $convertOneThirdToCash = false,
        public string $commissionAverage = '0',
        public string $overtimeAverage = '0',
        public string $recurringAdditions = '0',
        public string $otherDeductions = '0',
    ) {}

    public function toDomain(): VacationInput
    {
        return new VacationInput(
            monthlySalary: Money::fromDecimal($this->monthlySalary),
            acquisitionStartDate: new DateTimeImmutable($this->acquisitionStartDate),
            vacationStartDate: new DateTimeImmutable($this->vacationStartDate),
            unjustifiedAbsences: $this->unjustifiedAbsences,
            convertOneThirdToCash: $this->convertOneThirdToCash,
            commissionAverage: Money::fromDecimal($this->commissionAverage),
            overtimeAverage: Money::fromDecimal($this->overtimeAverage),
            recurringAdditions: Money::fromDecimal($this->recurringAdditions),
            otherDeductions: Money::fromDecimal($this->otherDeductions),
        );
    }

    public function toArray(): array
    {
        return [
            'monthly_salary' => $this->monthlySalary,
            'acquisition_start_date' => $this->acquisitionStartDate,
            'vacation_start_date' => $this->vacationStartDate,
            'unjustified_absences' => $this->unjustifiedAbsences,
            'convert_one_third_to_cash' => $this->convertOneThirdToCash,
            'commission_average' => $this->commissionAverage,
            'overtime_average' => $this->overtimeAverage,
            'recurring_additions' => $this->recurringAdditions,
            'other_deductions' => $this->otherDeductions,
        ];
    }
}
