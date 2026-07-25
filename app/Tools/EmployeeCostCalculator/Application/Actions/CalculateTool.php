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

    public function execute(array $d): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($d['salary']),
            Money::fromDecimal($d['variable_pay']),
            Money::fromDecimal($d['benefits']),
            $d['regime'],
            Percentage::fromString($d['rat']),
            Percentage::fromString($d['third_parties']),
            (int) ($d['monthly_hours'] ?? 220),
        ));
    }
}
