<?php

namespace App\Core\Access\Enums;

enum AccountRole: string
{
    case User = 'user';
    case Manager = 'manager';
    case Administrator = 'administrator';

    public function isInternal(): bool
    {
        return $this === self::Administrator;
    }
}
