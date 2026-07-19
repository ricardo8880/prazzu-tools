<?php

namespace App\Core\Access\Enums;

enum SubscriptionPlan: string
{
    case Free = 'free';
    case Plus = 'plus';

    public function grantsPlusFeatures(): bool
    {
        return $this === self::Plus;
    }
}
