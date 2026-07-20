<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Enums;

enum ComparisonPeriod: string
{
    case Monthly = 'monthly';
    case Annual = 'annual';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'Mensal',
            self::Annual => 'Anual',
        };
    }
}
