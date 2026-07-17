<?php

namespace App\Core\Organizations\Enums;

enum OrganizationMemberRole: string
{
    case Owner = 'owner';
    case Administrator = 'administrator';
    case Member = 'member';

    public function canManageOrganization(): bool
    {
        return $this === self::Owner || $this === self::Administrator;
    }
}
