<?php

namespace App\Core\Tools\Enums;

enum ToolStatus: string
{
    case Draft = 'draft';
    case Internal = 'internal';
    case Beta = 'beta';
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Deprecated = 'deprecated';
    case Retired = 'retired';

    public function isVisibleInCatalog(): bool
    {
        return in_array($this, [self::Beta, self::Active, self::Maintenance, self::Deprecated], true);
    }

    public function acceptsNewExecutions(): bool
    {
        return in_array($this, [self::Beta, self::Active], true);
    }
}
