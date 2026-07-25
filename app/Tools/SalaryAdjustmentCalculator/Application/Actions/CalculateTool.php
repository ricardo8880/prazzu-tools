<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\SalaryAdjustmentCalculator\Application\Data\CalculationInput;
use App\Tools\SalaryAdjustmentCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['current_salary']),
            Percentage::fromString($data['adjustment_rate']),
            Money::fromDecimal($data['fixed_addition']),
            (int) $data['retroactive_months'],
        ));
    }
}
