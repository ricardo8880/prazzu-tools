<?php

namespace App\Core\Acquisition\Domain\Enums;

enum AcquisitionContextToolPlacement: string
{
    case Featured = 'featured';
    case Recommended = 'recommended';
}
