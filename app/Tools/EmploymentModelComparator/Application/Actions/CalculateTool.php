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
        return $this->calculator->calculate(new CalculationInput(Money::fromDecimal($d['clt_gross']), Money::fromDecimal($d['clt_benefits']), Percentage::fromString($d['clt_employee_deductions']), Percentage::fromString($d['clt_company_burden']), Money::fromDecimal($d['pj_invoice']), Percentage::fromString($d['pj_taxes']), Money::fromDecimal($d['pj_expenses']), Money::fromDecimal($d['autonomous_gross']), Percentage::fromString($d['autonomous_deductions']), Percentage::fromString($d['autonomous_company_burden'])));
    }
}
