<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\TurnoverCalculator\Application\Data\CalculationInput;
use App\Tools\TurnoverCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            admissions: (int) $data['admissions'],
            terminations: (int) $data['terminations'],
            averageHeadcount: (int) $data['average_headcount'],
        ));
    }
}
