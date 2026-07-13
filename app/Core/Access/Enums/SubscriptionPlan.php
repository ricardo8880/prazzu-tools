<?php

namespace App\Core\Access\Enums;

enum SubscriptionPlan: string
{
    case Free = 'free';
    case Premium = 'premium';

    public function grantsPremiumTools(): bool
    {
        return $this === self::Premium;
    }
}
