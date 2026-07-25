<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\WorkingCapitalCalculator\Application\Data\CalculationInput;
use App\Tools\WorkingCapitalCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string, string> $data */
    public function execute(array $data): ToolCalculationResult
    {
        $money = static fn (string $key): Money => Money::fromDecimal($data[$key] ?? '0');

        return $this->calculator->calculate(new CalculationInput(
            $money('cash'), $money('receivables'), $money('inventory'), $money('other_current_assets'),
            $money('suppliers'), $money('other_operating_liabilities'), $money('loans'), $money('other_current_liabilities'),
        ));
    }
}
