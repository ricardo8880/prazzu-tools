<?php

declare(strict_types=1);

namespace App\Core\Dates\Contracts;

use App\Core\Dates\EffectivePeriod;

interface EffectiveDated
{
    public function effectivePeriod(): EffectivePeriod;
}
