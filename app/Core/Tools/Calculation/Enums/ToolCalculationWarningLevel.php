<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Enums;

enum ToolCalculationWarningLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
}
