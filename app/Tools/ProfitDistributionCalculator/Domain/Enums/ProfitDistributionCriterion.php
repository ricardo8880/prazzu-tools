<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Domain\Enums;

enum ProfitDistributionCriterion: string
{
    case Proportional = 'proportional';
    case DefinedAmounts = 'defined_amounts';
}
