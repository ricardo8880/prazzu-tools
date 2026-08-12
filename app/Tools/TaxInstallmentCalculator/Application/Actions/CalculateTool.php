<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\TaxInstallmentCalculator\Application\Data\CalculationInput;
use App\Tools\TaxInstallmentCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(CalculationInput $input): ToolCalculationResult
    {
        return $this->calculator->calculate($input);
    }
}
