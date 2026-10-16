<?php

declare(strict_types=1);

namespace App\Core\Money;

enum Currency: string
{
    case USD = 'USD';

    public function fractionDigits(): int
    {
        return 2;
    }

    public function symbol(): string
    {
        return '$';
    }
}
