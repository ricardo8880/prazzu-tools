<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum ProcessingMode: string
{
    case Synchronous = 'synchronous';
    case Queue = 'queue';
}
