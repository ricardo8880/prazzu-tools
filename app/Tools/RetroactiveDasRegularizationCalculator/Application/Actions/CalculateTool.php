<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\RetroactiveDasRegularizationCalculator\Application\Data\CalculationInput;
use App\Tools\RetroactiveDasRegularizationCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(CalculationInput $input): ToolCalculationResult
    {
        return $this->calculator->calculate($input);
    }
}
