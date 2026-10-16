<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\IncomeStatementGenerator\Application\Data\CalculationInput;
use App\Tools\IncomeStatementGenerator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}
    public function execute(array $d): ToolCalculationResult
    {
        $m=static fn(string $k): Money=>Money::fromDecimal((string)$d[$k]);
        return $this->calculator->calculate(new CalculationInput(
            (string)$d['name'],(string)$d['employee_id'],(string)$d['payer'],(int)$d['year'],$m('gross'),$m('federal_income_tax'),$m('social_security'),$m('medicare'),$m('state_local_tax'),$m('other_deductions')
        ));
    }
}
