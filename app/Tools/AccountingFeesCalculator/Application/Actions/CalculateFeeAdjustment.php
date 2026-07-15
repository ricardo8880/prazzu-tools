<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Domain\Calculators\FeeAdjustmentCalculator;
use App\Tools\AccountingFeesCalculator\Domain\Data\FeeAdjustmentResult;

final readonly class CalculateFeeAdjustment
{
    public function __construct(private FeeAdjustmentCalculator $calculator) {}

    public function execute(int $currentValueCents, float $percentage): FeeAdjustmentResult
    {
        return $this->calculator->calculate($currentValueCents, $percentage);
    }
}
