<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Enums;

enum DueDateAdjustment: string
{
    case None = 'none';
    case PreviousBusinessDay = 'previous_business_day';
    case NextBusinessDay = 'next_business_day';
}
