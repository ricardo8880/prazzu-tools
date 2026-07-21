<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums;

enum ProfitDistributionCriterion: string
{
    case Proportional = 'proportional';
    case DefinedAmounts = 'defined_amounts';
}
