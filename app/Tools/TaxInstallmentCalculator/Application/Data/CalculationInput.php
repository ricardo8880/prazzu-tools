<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    /** @param list<array{name:string,debt:string,entry:string,installments:int,monthly_charge:string}> $scenarios */
    public function __construct(public array $scenarios) {}

    public function toArray(): array
    {
        return ['scenarios' => $this->scenarios];
    }
}
