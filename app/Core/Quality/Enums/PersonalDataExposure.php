<?php

declare(strict_types=1);

namespace App\Core\Quality\Enums;

enum PersonalDataExposure: string
{
    case None = 'none';
    case Common = 'common';
    case Sensitive = 'sensitive';
}
