<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\PayslipGenerator\Application\Data\CalculationInput;
use App\Tools\PayslipGenerator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $d): ToolCalculationResult
    {
        $m = static fn (string $key): Money => Money::fromDecimal((string) $d[$key]);
        return $this->calculator->calculate(new CalculationInput(
            employeeName: (string) $d['employee_name'],
            employeeId: (string) $d['employee_id'],
            employer: (string) $d['employer'],
            payPeriod: (string) $d['pay_period'],
            regularPay: $m('regular_pay'),
            overtimePay: $m('overtime_pay'),
            otherEarnings: $m('other_earnings'),
            federalIncomeTax: $m('federal_income_tax'),
            socialSecurity: $m('social_security'),
            medicare: $m('medicare'),
            stateLocalTax: $m('state_local_tax'),
            otherDeductions: $m('other_deductions'),
        ));
    }
}
