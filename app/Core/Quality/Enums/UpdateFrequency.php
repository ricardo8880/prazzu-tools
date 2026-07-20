<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum UpdateFrequency: string
{
    case Rare = 'rare';
    case Annual = 'annual';
    case Monthly = 'monthly';
    case Unpredictable = 'unpredictable';
}
