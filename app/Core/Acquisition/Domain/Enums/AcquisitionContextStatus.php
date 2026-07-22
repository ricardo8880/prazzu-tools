<?php

namespace App\Core\Acquisition\Domain\Enums;

enum AcquisitionContextStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function isAvailable(): bool
    {
        return $this === self::Active;
    }
}
