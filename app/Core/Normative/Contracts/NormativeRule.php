<?php

declare(strict_types=1);

namespace App\Core\Normative\Contracts;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Normative\NormativeRuleMetadata;

interface NormativeRule extends EffectiveDated
{
    public function normativeMetadata(): NormativeRuleMetadata;
}
