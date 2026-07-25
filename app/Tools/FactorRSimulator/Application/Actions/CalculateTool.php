<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\FactorRSimulator\Application\Data\CalculationInput;
use App\Tools\FactorRSimulator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['payroll_12']),
            Money::fromDecimal($data['revenue_12']),
        ));
    }
}
