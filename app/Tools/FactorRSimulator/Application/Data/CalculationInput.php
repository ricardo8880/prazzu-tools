<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public Money $payroll12, public Money $revenue12) {}

    public function toArray(): array
    {
        return ['payroll_12' => $this->payroll12->minorAmount(), 'revenue_12' => $this->revenue12->minorAmount()];
    }
}
