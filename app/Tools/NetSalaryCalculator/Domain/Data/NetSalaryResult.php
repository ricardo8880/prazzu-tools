<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;

final readonly class NetSalaryResult
{
    public function __construct(
        public Money $taxableGross,
        public Money $totalEarnings,
        public Money $socialSecurityBase,
        public Money $socialSecurityWithheld,
        public Money $legalIrrfDeductions,
        public Money $simplifiedIrrfDeduction,
        public string $irrfDeductionMethod,
        public Money $irrfBase,
        public Money $irrfBeforeReduction,
        public Money $irrfReduction,
        public Money $irrfWithheld,
        public Money $userDiscounts,
        public Money $totalDiscounts,
        public Money $netSalary,
        public CalculationMemory $memory,
    ) {}
}
