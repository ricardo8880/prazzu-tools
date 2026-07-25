<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\EmployerInssCalculator\Application\Data\CalculationInput;
use App\Tools\EmployerInssCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['payroll']),
            $data['regime'],
            Percentage::fromString($data['adjusted_rat']),
            Percentage::fromString($data['third_parties']),
        ));
    }
}
