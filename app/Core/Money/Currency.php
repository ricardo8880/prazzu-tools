<?php

declare(strict_types=1);

namespace App\Core\Money;

enum Currency: string
{
    case BRL = 'BRL';

    public function fractionDigits(): int
    {
        return match ($this) {
            self::BRL => 2,
        };

    }

    public function symbol(): string
    {
        return match ($this) {
            self::BRL => 'R$',
        };
    }
}
