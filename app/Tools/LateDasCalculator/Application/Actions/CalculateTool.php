<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\LateDasCalculator\Application\Data\CalculationInput;
use App\Tools\LateDasCalculator\Domain\Services\Calculator;
use DateTimeImmutable;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    public function execute(array $d): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(Money::fromDecimal($d['principal']), new DateTimeImmutable($d['due_date']), new DateTimeImmutable($d['payment_date']), Percentage::fromString($d['accumulated_selic'])));
    }
}
