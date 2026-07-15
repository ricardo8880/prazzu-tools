<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Enums;

enum TaxRegime: string
{
    case Mei = 'mei';
    case SimplesNacional = 'simples_nacional';
    case LucroPresumido = 'lucro_presumido';
    case LucroReal = 'lucro_real';

    public function label(): string
    {
        return match ($this) {
            self::Mei => 'MEI',
            self::SimplesNacional => 'Simples Nacional',
            self::LucroPresumido => 'Lucro Presumido',
            self::LucroReal => 'Lucro Real',
        };
    }

    public function baseFeeInCents(): int
    {
        return match ($this) {
            self::Mei => 25_000,
            self::SimplesNacional => 65_000,
            self::LucroPresumido => 100_000,
            self::LucroReal => 180_000,
        };
    }
}
