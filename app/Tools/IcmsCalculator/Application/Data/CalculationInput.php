<?php

declare(strict_types=1);

namespace App\Tools\IcmsCalculator\Application\Data; use App\Core\Tools\Contracts\ToolCalculationInput; final readonly class CalculationInput implements ToolCalculationInput { public function __construct(public string $value,public string $rate,public string $reduction='0',public bool $valueExcludesIcms=false){} public function toArray():array{return ['value'=>$this->value,'rate'=>$this->rate,'reduction'=>$this->reduction,'value_excludes_icms'=>$this->valueExcludesIcms];}}
