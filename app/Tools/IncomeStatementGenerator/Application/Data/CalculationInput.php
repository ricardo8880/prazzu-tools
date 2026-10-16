<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $name,
        public string $employeeId,
        public string $payer,
        public int $year,
        public Money $gross,
        public Money $federalIncomeTax,
        public Money $socialSecurity,
        public Money $medicare,
        public Money $stateLocalTax,
        public Money $otherDeductions,
    ) {}

    public function toArray(): array
    {
        return [
            'name'=>$this->name,'employee_id'=>$this->employeeId,'payer'=>$this->payer,'year'=>$this->year,
            'gross'=>$this->gross->minorAmount(),'federal_income_tax'=>$this->federalIncomeTax->minorAmount(),
            'social_security'=>$this->socialSecurity->minorAmount(),'medicare'=>$this->medicare->minorAmount(),
            'state_local_tax'=>$this->stateLocalTax->minorAmount(),'other_deductions'=>$this->otherDeductions->minorAmount(),
        ];
    }
}
