<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum PartyDocumentType: string
{
    case Cpf = 'cpf';
    case Cnpj = 'cnpj';

    public function label(): string
    {
        return match ($this) {
            self::Cpf => 'CPF',
            self::Cnpj => 'CNPJ',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            self::Cpf->value => self::Cpf->label(),
            self::Cnpj->value => self::Cnpj->label(),
        ];
    }
}
