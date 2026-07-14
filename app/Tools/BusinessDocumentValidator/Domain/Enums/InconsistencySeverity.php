<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Enums;

enum InconsistencySeverity: string
{
    case Information = 'information';
    case Warning = 'warning';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Information => 'Informação',
            self::Warning => 'Alerta',
            self::Error => 'Erro',
        };
    }
}
