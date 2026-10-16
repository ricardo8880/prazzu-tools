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
            self::Services => 'Professional services',
            self::Commerce => 'Retail / commerce',
            self::Industry => 'Manufacturing',
            self::Construction => 'Construction',
            self::Healthcare => 'Healthcare',
            self::DigitalBusiness => 'Digital business',
            self::Other => 'Other',
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
