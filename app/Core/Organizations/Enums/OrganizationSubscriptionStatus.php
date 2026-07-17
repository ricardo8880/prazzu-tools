<?php

namespace App\Core\Organizations\Enums;

enum OrganizationSubscriptionStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case PastDue = 'past_due';
    case Suspended = 'suspended';
    case Canceled = 'canceled';

    public function grantsPlusAccess(): bool
    {
        return $this === self::Active;
    }
}
