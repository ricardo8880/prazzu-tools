<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Tools\AccountingFeesCalculator\Domain\Data\FeeAdjustmentResult;

final class FeeAdjustmentCalculator
{
    public function calculate(int $currentValueCents, float $percentage): FeeAdjustmentResult
    {
        if ($currentValueCents <= 0) {
            throw new InvalidValue('O valor atual deve ser maior que zero.');
        }

        if ($percentage < -100 || $percentage > 1000) {
            throw new InvalidValue('O percentual deve estar entre -100% e 1.000%.');
        }

        $difference = (int) round($currentValueCents * ($percentage / 100));

        return new FeeAdjustmentResult(
            currentValueCents: $currentValueCents,
            percentage: $percentage,
            differenceCents: $difference,
            adjustedValueCents: $currentValueCents + $difference,
        );
    }
}
