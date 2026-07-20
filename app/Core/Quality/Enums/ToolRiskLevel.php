<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum ToolRiskLevel: string
{
    case Low = 'low';
    case Moderate = 'moderate';
    case High = 'high';
    case Critical = 'critical';
}
