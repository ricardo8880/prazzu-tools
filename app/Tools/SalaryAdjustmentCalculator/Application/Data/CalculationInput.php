<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $currentSalary,
        public Percentage $adjustmentRate,
        public Money $fixedAddition,
        public int $retroactiveMonths,
    ) {}

    public function toArray(): array
    {
        return [
            'current_salary' => $this->currentSalary->minorAmount(),
            'adjustment_rate' => $this->adjustmentRate->toDecimalString(),
            'fixed_addition' => $this->fixedAddition->minorAmount(),
            'retroactive_months' => $this->retroactiveMonths,
        ];
    }
}
