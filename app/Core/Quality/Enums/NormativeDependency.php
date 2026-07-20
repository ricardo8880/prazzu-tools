<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum NormativeDependency: string
{
    case None = 'none';
    case Low = 'low';
    case High = 'high';
}
