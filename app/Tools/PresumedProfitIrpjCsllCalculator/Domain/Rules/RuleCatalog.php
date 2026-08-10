<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Rules;

final class RuleCatalog
{
    /** @return list<PresumedProfitRule> */
    public static function all(): array
    {
        return [new PresumedProfitRule];
    }
}
