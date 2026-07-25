<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $salary, public Money $benefits, public string $regime,
        public Percentage $rat, public Percentage $thirdParties,
    ) {}

    public function toArray(): array
    {
        return [
            'salary' => $this->salary->minorAmount(), 'benefits' => $this->benefits->minorAmount(), 'regime' => $this->regime,
            'rat' => $this->rat->toDecimalString(), 'third_parties' => $this->thirdParties->toDecimalString(),
        ];
    }
}
