<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Enums;

enum BusinessActivity: string
{
    case Commerce = 'commerce';
    case Industry = 'industry';
    case Services = 'services';
    case AccountingServices = 'accounting_services';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Commerce => 'Comércio',
            self::Industry => 'Indústria',
            self::Services => 'Prestação de serviços',
            self::AccountingServices => 'Serviços contábeis',
            self::Mixed => 'Atividade mista',
        };
    }
}
