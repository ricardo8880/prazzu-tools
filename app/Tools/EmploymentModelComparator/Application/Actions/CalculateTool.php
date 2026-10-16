<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\EmploymentModelComparator\Application\Data\CalculationInput;
use App\Tools\EmploymentModelComparator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $d): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal((string) $d['w2_gross']),
            Money::fromDecimal((string) $d['w2_benefits']),
            Percentage::fromString((string) $d['w2_employee_deductions']),
            Percentage::fromString((string) $d['w2_employer_burden']),
            Money::fromDecimal((string) $d['contractor_revenue']),
            Percentage::fromString((string) $d['contractor_tax_rate']),
            Money::fromDecimal((string) $d['contractor_expenses']),
            Money::fromDecimal((string) $d['self_employed_revenue']),
            Percentage::fromString((string) $d['self_employed_tax_rate']),
            Money::fromDecimal((string) $d['self_employed_expenses']),
        ));
    }
}
