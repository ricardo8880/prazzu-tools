<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $hourlyRate,
        public string $accruedPtoHours,
        public string $plannedPtoHours,
        public string $accrualHoursPerPayPeriod = '0',
        public int $payPeriodsBeforeLeave = 0,
        public string $payoutPercent = '100',
    ) {}

    /** @return array<string,int|string> */
    public function toArray(): array { return get_object_vars($this); }
    public function hourlyMoney(): Money { return Money::fromDecimal($this->hourlyRate); }
    public function payoutRate(): Percentage { return Percentage::fromString($this->payoutPercent); }
}
