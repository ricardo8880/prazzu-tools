<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $salary,
        public Money $variablePay,
        public Money $benefits,
        public string $futaProfile,
        public Percentage $sutaRate,
        public Percentage $otherEmployerCostRate,
        public int $monthlyHours = 173,
    ) {}

    public function toArray(): array
    {
        return [
            'salary' => $this->salary->minorAmount(),
            'variable_pay' => $this->variablePay->minorAmount(),
            'benefits' => $this->benefits->minorAmount(),
            'futa_profile' => $this->futaProfile,
            'suta_rate' => $this->sutaRate->toDecimalString(),
            'other_employer_cost_rate' => $this->otherEmployerCostRate->toDecimalString(),
            'monthly_hours' => $this->monthlyHours,
        ];
    }
}
