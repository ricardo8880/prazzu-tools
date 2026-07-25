<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $payroll,
        public string $regime,
        public Percentage $adjustedRat,
        public Percentage $thirdParties,
    ) {}

    public function toArray(): array
    {
        return [
            'payroll' => $this->payroll->minorAmount(),
            'regime' => $this->regime,
            'adjusted_rat' => $this->adjustedRat->toDecimalString(),
            'third_parties' => $this->thirdParties->toDecimalString(),
        ];
    }
}
