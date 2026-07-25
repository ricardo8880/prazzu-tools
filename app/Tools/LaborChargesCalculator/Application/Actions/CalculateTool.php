<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\LaborChargesCalculator\Application\Data\CalculationInput;
use App\Tools\LaborChargesCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            Money::fromDecimal($data['salary']), Money::fromDecimal($data['benefits']), $data['regime'],
            Percentage::fromString($data['rat']), Percentage::fromString($data['third_parties']),
        ));
    }
}
