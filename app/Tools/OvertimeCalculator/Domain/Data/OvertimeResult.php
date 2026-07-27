<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;

final readonly class OvertimeResult
{
    public function __construct(
        public Money $hourlyRate,
        public Money $overtime50,
        public Money $overtime100,
        public Money $customOvertime,
        public Money $nightPremium,
        public Money $nightOvertime,
        public Money $variableTotal,
        public Money $dsr,
        public Money $monthlyTotal,
        public Money $thirteenthReflex,
        public Money $vacationReflex,
        public Money $vacationThirdReflex,
        public Money $fgtsEstimate,
        public CalculationMemory $memory,
    ) {}
}
