<?php

declare(strict_types=1);
namespace App\Tools\IssCalculator\Application\Data;
use App\Core\Tools\Contracts\ToolCalculationInput;
final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public array $services, public array $municipalityScenarios = []) {}
    public function toArray(): array { return ['services'=>$this->services,'municipality_scenarios'=>$this->municipalityScenarios]; }
}
