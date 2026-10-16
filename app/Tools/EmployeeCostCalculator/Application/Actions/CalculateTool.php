<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\EmployeeCostCalculator\Application\Data\CalculationInput;
use App\Tools\EmployeeCostCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string,mixed> $data */
    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal((string) $data['salary']),
            Money::fromDecimal((string) $data['variable_pay']),
            Money::fromDecimal((string) $data['benefits']),
            (string) $data['futa_profile'],
            Percentage::fromString((string) $data['suta_rate']),
            Percentage::fromString((string) $data['other_employer_cost_rate']),
            (int) ($data['monthly_hours'] ?? 173),
        ));
    }
}
