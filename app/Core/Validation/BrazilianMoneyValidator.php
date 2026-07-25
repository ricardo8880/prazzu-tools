<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Money\Money;
use Throwable;

final class BrazilianMoneyValidator
{
    public function isValid(mixed $value): bool
    {
        return $this->parse($value) !== null;
    }

    /** @param array<int, string> $parameters */
    public function hasMinimum(mixed $value, array $parameters): bool
    {
        $amount = $this->parse($value);
        $minimum = $this->parse($parameters[0] ?? null);

        return $amount !== null
            && $minimum !== null
            && $amount->minorAmount() >= $minimum->minorAmount();
    }

    private function parse(mixed $value): ?Money
    {
        if ((! is_string($value) && ! is_int($value)) || trim((string) $value) === '') {
            return null;
        }

        try {
            return Money::fromDecimal((string) $value);
        } catch (Throwable) {
            return null;
        }
    }
}
