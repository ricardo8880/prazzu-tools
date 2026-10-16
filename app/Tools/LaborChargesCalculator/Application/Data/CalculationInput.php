<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public Money $salary, public Money $benefits, public string $futaProfile, public Percentage $sutaRate, public Percentage $otherEmployerCostRate) {}

    public function toArray(): array
    {
        return ['salary'=>$this->salary->minorAmount(),'benefits'=>$this->benefits->minorAmount(),'futa_profile'=>$this->futaProfile,'suta_rate'=>$this->sutaRate->toDecimalString(),'other_employer_cost_rate'=>$this->otherEmployerCostRate->toDecimalString()];
    }
}
