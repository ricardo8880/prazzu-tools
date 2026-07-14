<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Enums;

enum RegistryLookupStatus: string
{
    case Found = 'found';
    case NotFound = 'not_found';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::Found => 'Consulta realizada',
            self::NotFound => 'CNPJ não encontrado',
            self::Unavailable => 'Consulta indisponível',
        };
    }
}
