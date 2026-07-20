<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum PersistenceMode: string
{
    case Temporary = 'temporary';
    case History = 'history';
    case Document = 'document';
}
