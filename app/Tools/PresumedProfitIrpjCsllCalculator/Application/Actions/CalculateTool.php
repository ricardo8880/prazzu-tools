<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data\CalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(CalculationInput $input): ToolCalculationResult
    {
        return $this->calculator->calculate($input);
    }
}
