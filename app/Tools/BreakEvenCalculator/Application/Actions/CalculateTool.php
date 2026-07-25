<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\BreakEvenCalculator\Application\Data\CalculationInput;
use App\Tools\BreakEvenCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['fixed_costs']),
            Money::fromDecimal($data['sale_price']),
            Money::fromDecimal($data['variable_cost']),
        ));
    }
}
