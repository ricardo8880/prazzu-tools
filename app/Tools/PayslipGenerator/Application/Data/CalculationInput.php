<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $employeeName,
        public string $employeeId,
        public string $employer,
        public string $payPeriod,
        public Money $regularPay,
        public Money $overtimePay,
        public Money $otherEarnings,
        public Money $federalIncomeTax,
        public Money $socialSecurity,
        public Money $medicare,
        public Money $stateLocalTax,
        public Money $otherDeductions,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_name' => $this->employeeName,
            'employee_id' => $this->employeeId,
            'employer' => $this->employer,
            'pay_period' => $this->payPeriod,
            'regular_pay' => $this->regularPay->minorAmount(),
            'overtime_pay' => $this->overtimePay->minorAmount(),
            'other_earnings' => $this->otherEarnings->minorAmount(),
            'federal_income_tax' => $this->federalIncomeTax->minorAmount(),
            'social_security' => $this->socialSecurity->minorAmount(),
            'medicare' => $this->medicare->minorAmount(),
            'state_local_tax' => $this->stateLocalTax->minorAmount(),
            'other_deductions' => $this->otherDeductions->minorAmount(),
        ];
    }
}
