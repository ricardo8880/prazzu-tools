<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum ResultRisk: string
{
    case Informational = 'informational';
    case Financial = 'financial';
    case Labor = 'labor';
    case Tax = 'tax';
}
