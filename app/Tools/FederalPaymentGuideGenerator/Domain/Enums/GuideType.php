<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Enums;

enum GuideType: string
{
    case Darf = 'darf';
    case Gps = 'gps';
}
