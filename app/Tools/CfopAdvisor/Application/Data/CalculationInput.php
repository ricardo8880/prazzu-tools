<?php

declare(strict_types=1);
namespace App\Tools\CfopAdvisor\Application\Data;
use App\Core\Tools\Contracts\ToolCalculationInput;
final readonly class CalculationInput implements ToolCalculationInput { public function __construct(public string $cfop) {} public function toArray(): array { return ['cfop'=>$this->cfop]; } }
