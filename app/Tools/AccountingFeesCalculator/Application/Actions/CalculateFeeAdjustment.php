<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\FeeAdjustmentCalculator;
use App\Tools\AccountingFeesCalculator\Domain\Data\FeeAdjustmentResult;

final readonly class CalculateFeeAdjustment
{
    public function __construct(private FeeAdjustmentCalculator $calculator) {}

    public function execute(int $currentValueCents, string $percentage): FeeAdjustmentResult
    {
        return $this->calculator->calculate(
            currentValue: Money::fromMinor($currentValueCents),
            percentage: Percentage::fromString($percentage),
        );
    }
}
