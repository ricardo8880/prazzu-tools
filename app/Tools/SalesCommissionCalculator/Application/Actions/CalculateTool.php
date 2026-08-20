<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\SalesCommissionCalculator\Application\Data\CalculationInput;
use App\Tools\SalesCommissionCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['sales']),
            Percentage::fromString($data['rate']),
            Money::fromDecimal($data['goal'] ?? '0'),
            Percentage::fromString((string) ($data['goal_bonus_rate'] ?? '0')),
            Money::fromDecimal($data['reversals'] ?? '0,00'),
        ));
    }
}
