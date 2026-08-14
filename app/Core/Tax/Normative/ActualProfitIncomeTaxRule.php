<?php

declare(strict_types=1);

namespace App\Core\Tax\Normative;

use App\Core\Money\Percentage;

final readonly class ActualProfitIncomeTaxRule
{
    public const VERSION = '1.0.0';
    public function irpjRate(): Percentage { return Percentage::fromString('15'); }
    public function irpjAdditionalRate(): Percentage { return Percentage::fromString('10'); }
    public function csllRate(): Percentage { return Percentage::fromString('9'); }
    public function lossCompensationLimit(): Percentage { return Percentage::fromString('30'); }
    public function additionalThresholdMinorPerMonth(): int { return 2_000_000; }
}
