<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\WorkIncomeStatementGenerator\Application\Data\CalculationInput;
use App\Tools\WorkIncomeStatementGenerator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $d): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput($d['name'], $d['document'], $d['employer'], $d['occupation'], $d['start_date'], Money::fromDecimal($d['monthly_income']), $d['city'], $d['issue_date']));
    }
}
