<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Enums;

enum TaxRegime: string
{
    case SoleProprietor = 'sole_proprietor';
    case Partnership = 'partnership';
    case SCorporation = 's_corporation';
    case CCorporation = 'c_corporation';
    case Nonprofit = 'nonprofit';

    public function label(): string
    {
        return match ($this) {
            self::SoleProprietor => 'Sole proprietor / single-member LLC',
            self::Partnership => 'Partnership / multi-member LLC',
            self::SCorporation => 'S corporation',
            self::CCorporation => 'C corporation',
            self::Nonprofit => 'Nonprofit organization',
        };
    }

    public function baseFeeInCents(): int
    {
        return match ($this) {
            self::SoleProprietor => 30_000,
            self::Partnership => 55_000,
            self::SCorporation => 70_000,
            self::CCorporation => 90_000,
            self::Nonprofit => 90_000,
        };
    }
}
