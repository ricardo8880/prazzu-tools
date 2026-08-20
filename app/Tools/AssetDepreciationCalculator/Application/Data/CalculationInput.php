<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    /** @param list<array{name:string,value:string,residual_value?:string,useful_life_years:int,method:string}> $assets */
    public function __construct(public array $assets) {}

    public function toArray(): array
    {
        return ['assets' => $this->assets];
    }
}
