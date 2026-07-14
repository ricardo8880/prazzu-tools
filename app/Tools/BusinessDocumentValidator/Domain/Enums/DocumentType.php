<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Enums;

enum DocumentType: string
{
    case Automatic = 'automatic';
    case Cpf = 'cpf';
    case Cnpj = 'cnpj';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => 'Detecção automática',
            self::Cpf => 'CPF',
            self::Cnpj => 'CNPJ',
        };
    }
}
