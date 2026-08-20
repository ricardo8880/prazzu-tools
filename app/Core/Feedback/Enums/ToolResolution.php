<?php

declare(strict_types=1);

namespace App\Core\Feedback\Enums;

enum ToolResolution: string
{
    case Yes = 'yes';
    case Partially = 'partially';
    case No = 'no';

    public function label(): string
    {
        return match ($this) {
            self::Yes => 'Sim',
            self::Partially => 'Parcialmente',
            self::No => 'Não',
        };
    }
}
