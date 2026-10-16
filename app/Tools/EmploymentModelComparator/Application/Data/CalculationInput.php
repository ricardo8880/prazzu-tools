<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $w2Gross,
        public Money $w2Benefits,
        public Percentage $w2EmployeeDeductions,
        public Percentage $w2EmployerBurden,
        public Money $contractorRevenue,
        public Percentage $contractorTaxRate,
        public Money $contractorExpenses,
        public Money $selfEmployedRevenue,
        public Percentage $selfEmployedTaxRate,
        public Money $selfEmployedExpenses,
    ) {}

    public function toArray(): array
    {
        return [
            'w2_gross' => $this->w2Gross->minorAmount(),
            'w2_benefits' => $this->w2Benefits->minorAmount(),
            'w2_employee_deductions' => $this->w2EmployeeDeductions->toDecimalString(),
            'w2_employer_burden' => $this->w2EmployerBurden->toDecimalString(),
            'contractor_revenue' => $this->contractorRevenue->minorAmount(),
            'contractor_tax_rate' => $this->contractorTaxRate->toDecimalString(),
            'contractor_expenses' => $this->contractorExpenses->minorAmount(),
            'self_employed_revenue' => $this->selfEmployedRevenue->minorAmount(),
            'self_employed_tax_rate' => $this->selfEmployedTaxRate->toDecimalString(),
            'self_employed_expenses' => $this->selfEmployedExpenses->minorAmount(),
        ];
    }
}
