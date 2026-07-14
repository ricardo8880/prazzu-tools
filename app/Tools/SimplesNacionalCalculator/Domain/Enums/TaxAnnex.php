<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Enums;

enum TaxAnnex: string
{
    case I = 'I';
    case II = 'II';
    case III = 'III';
    case IV = 'IV';
    case V = 'V';

    public function label(): string
    {
        return match ($this) {
            self::I => 'Anexo I — Comércio',
            self::II => 'Anexo II — Indústria',
            self::III => 'Anexo III — Serviços',
            self::IV => 'Anexo IV — Serviços com CPP fora do DAS',
            self::V => 'Anexo V — Serviços',
        };
    }
}
