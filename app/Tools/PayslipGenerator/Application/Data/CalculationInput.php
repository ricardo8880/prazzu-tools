<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $name, public string $document, public string $employer, public string $competence, public Money $salary, public Money $otherEarnings, public Money $inss, public Money $irrf, public Money $otherDeductions) {}

    public function toArray(): array
    {
        return ['name' => $this->name, 'document' => $this->document, 'employer' => $this->employer, 'competence' => $this->competence, 'salary' => $this->salary->minorAmount(), 'other_earnings' => $this->otherEarnings->minorAmount(), 'inss' => $this->inss->minorAmount(), 'irrf' => $this->irrf->minorAmount(), 'other_deductions' => $this->otherDeductions->minorAmount()];
    }
}
