<?php

declare(strict_types=1);

namespace App\Tools\SefazFiscalValidator\Application\Data; use App\Core\Tools\Contracts\ToolCalculationInput; final readonly class CalculationInput implements ToolCalculationInput { public function __construct(public string $accessKey){} public function toArray(): array{return ['access_key'=>$this->accessKey];}}
