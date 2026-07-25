<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $fixedCosts,
        public Money $salePrice,
        public Money $variableCost,
    ) {}

    public function toArray(): array
    {
        return [
            'fixed_costs' => $this->fixedCosts->minorAmount(),
            'sale_price' => $this->salePrice->minorAmount(),
            'variable_cost' => $this->variableCost->minorAmount(),
        ];
    }
}
