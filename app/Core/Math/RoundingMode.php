<?php

declare(strict_types=1);

namespace App\Core\Math;

enum RoundingMode: string
{
    case Down = 'down';
    case Up = 'up';
    case HalfUp = 'half_up';
    case HalfDown = 'half_down';
    case HalfEven = 'half_even';
}
