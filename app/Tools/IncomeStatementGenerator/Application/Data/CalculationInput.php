<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $name, public string $document, public string $payer, public int $year, public Money $gross, public Money $inss, public Money $irrf, public Money $otherDeductions) {}

    public function toArray(): array
    {
        return ['name' => $this->name, 'document' => $this->document, 'payer' => $this->payer, 'year' => $this->year, 'gross' => $this->gross->minorAmount(), 'inss' => $this->inss->minorAmount(), 'irrf' => $this->irrf->minorAmount(), 'other_deductions' => $this->otherDeductions->minorAmount()];
    }
}
