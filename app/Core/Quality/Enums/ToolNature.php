<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum ToolNature: string
{
    case Calculation = 'calculation';
    case Validation = 'validation';
    case Generation = 'generation';
    case Comparison = 'comparison';
    case Conversion = 'conversion';
}
