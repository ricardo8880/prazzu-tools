<?php

namespace App\Core\Organizations\Enums;

enum OrganizationMemberStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
