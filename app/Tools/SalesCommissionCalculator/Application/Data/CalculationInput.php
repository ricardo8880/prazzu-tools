<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $sales,
        public Percentage $rate,
        public Money $goal,
        public Percentage $goalBonusRate,
    ) {}

    public function toArray(): array
    {
        return [
            'sales' => $this->sales->minorAmount(),
            'rate' => $this->rate->toDecimalString(),
            'goal' => $this->goal->minorAmount(),
            'goal_bonus_rate' => $this->goalBonusRate->toDecimalString(),
        ];
    }
}
