<?php

namespace App\Core\Organizations\Enums;

enum OrganizationInvitationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Revoked = 'revoked';
    case Expired = 'expired';

    public function canBeAccepted(): bool
    {
        return $this === self::Pending;
    }
}
