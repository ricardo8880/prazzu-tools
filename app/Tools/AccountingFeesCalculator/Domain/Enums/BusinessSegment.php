<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Enums;

enum BusinessSegment: string
{
    case Services = 'services';
    case Commerce = 'commerce';
    case Industry = 'industry';
    case Construction = 'construction';
    case Healthcare = 'healthcare';
    case DigitalBusiness = 'digital_business';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Services => 'Prestação de serviços',
            self::Commerce => 'Comércio',
            self::Industry => 'Indústria',
            self::Construction => 'Construção civil',
            self::Healthcare => 'Saúde',
            self::DigitalBusiness => 'Negócios digitais',
            self::Other => 'Outro segmento',
        };
    }

    public function multiplierBasisPoints(): int
    {
        return match ($this) {
            self::Services, self::DigitalBusiness => 10_000,
            self::Commerce, self::Other => 10_500,
            self::Healthcare => 11_000,
            self::Industry => 12_000,
            self::Construction => 12_500,
        };
    }
}
