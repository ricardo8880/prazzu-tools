<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Rules;

final class VacationEntitlementRule
{
    public function entitledDays(int $unjustifiedAbsences): int
    {
        return match (true) {
            $unjustifiedAbsences <= 5 => 30,
            $unjustifiedAbsences <= 14 => 24,
            $unjustifiedAbsences <= 23 => 18,
            $unjustifiedAbsences <= 32 => 12,
            default => 0,
        };
    }
}
