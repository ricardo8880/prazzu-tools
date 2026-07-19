<?php

declare(strict_types=1);

namespace App\Core\Tools\Enums;

enum ToolFeatureTier: string
{
    case Essential = 'essential';
    case Plus = 'plus';

    public function label(): string
    {
        return match ($this) {
            self::Essential => 'Essencial',
            self::Plus => 'Prazzu Plus',
        };
    }
}
