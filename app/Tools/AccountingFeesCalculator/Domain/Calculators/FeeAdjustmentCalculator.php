<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\AccountingFeesCalculator\Domain\Data\FeeAdjustmentResult;

final class FeeAdjustmentCalculator
{
    public function calculate(Money $currentValue, Percentage $percentage): FeeAdjustmentResult
    {
        if ($currentValue->minorAmount() <= 0) {
            throw new InvalidValue('The current amount must be greater than zero.');
        }

        $percentageUnits = $percentage->millionthsOfPercent();

        if (
            $percentageUnits < Percentage::fromString('-100')->millionthsOfPercent()
            || $percentageUnits > Percentage::fromString('1000')->millionthsOfPercent()
        ) {
            throw new InvalidValue('The percentage must be between -100% and 1,000%.');
        }

        $difference = $currentValue->percentage($percentage, RoundingMode::HalfUp);

        return new FeeAdjustmentResult(
            currentValue: $currentValue,
            percentage: $percentage,
            difference: $difference,
            adjustedValue: $currentValue->add($difference),
        );
    }
}
