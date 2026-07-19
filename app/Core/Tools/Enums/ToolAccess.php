<?php

namespace App\Core\Tools\Enums;

enum ToolAccess: string
{
    case Free = 'free';
    case Internal = 'internal';

    public function isPublic(): bool
    {
        return $this === self::Free;
    }
}
