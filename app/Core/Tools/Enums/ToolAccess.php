<?php

namespace App\Core\Tools\Enums;

enum ToolAccess: string
{
    case Free = 'free';
    case Premium = 'premium';
    case Authenticated = 'authenticated';
    case Internal = 'internal';

    public function isPublic(): bool
    {
        return $this === self::Free || $this === self::Premium;
    }
}
