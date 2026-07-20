<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Enums;

enum TaxRegime: string
{
    case SimplesNacional = 'simples_nacional';
    case PresumedProfit = 'lucro_presumido';
    case ActualProfit = 'lucro_real';

    public function label(): string
    {
        return match ($this) {
            self::SimplesNacional => 'Simples Nacional',
            self::PresumedProfit => 'Lucro Presumido',
            self::ActualProfit => 'Lucro Real',
        };
    }
}
