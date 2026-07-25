<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public Money $salary, public Money $benefits, public Percentage $monthlyBurden, public Money $exam, public Money $recruitment, public Money $equipment, public Money $training) {}

    public function toArray(): array
    {
        return ['salary' => $this->salary->minorAmount(), 'benefits' => $this->benefits->minorAmount(), 'monthly_burden' => $this->monthlyBurden->toDecimalString(), 'exam' => $this->exam->minorAmount(), 'recruitment' => $this->recruitment->minorAmount(), 'equipment' => $this->equipment->minorAmount(), 'training' => $this->training->minorAmount()];
    }
}
