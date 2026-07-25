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
        $m = fn ($k) => Money::fromDecimal($d[$k]);

        return $this->calculator->calculate(new CalculationInput($d['name'], $d['document'], $d['payer'], (int) $d['year'], $m('gross'), $m('inss'), $m('irrf'), $m('other_deductions')));
    }
}
