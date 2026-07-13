<?php

declare(strict_types=1);

namespace App\Core\Math;

use App\Core\Exceptions\InvalidValue;

final class IntegerRounding
{
    public static function divide(int $numerator, int $denominator, RoundingMode $mode = RoundingMode::HalfUp): int
    {
        if ($denominator === 0) {
            throw new InvalidValue('O divisor não pode ser zero.');
        }

        if ($numerator === 0) {
            return 0;
        }

        $negative = ($numerator < 0) xor ($denominator < 0);
        $absoluteNumerator = abs($numerator);
        $absoluteDenominator = abs($denominator);
        $quotient = intdiv($absoluteNumerator, $absoluteDenominator);
        $remainder = $absoluteNumerator % $absoluteDenominator;

        if ($remainder === 0) {
            return $negative ? -$quotient : $quotient;
        }

        $comparison = $remainder <=> ($absoluteDenominator - $remainder);

        $increment = match ($mode) {
            RoundingMode::Down => false,
            RoundingMode::Up => true,
            RoundingMode::HalfUp => $comparison >= 0,
            RoundingMode::HalfDown => $comparison > 0,
            RoundingMode::HalfEven => $comparison > 0
                || ($comparison === 0 && ($quotient % 2) !== 0),
        };

        $rounded = $quotient + ($increment ? 1 : 0);

        return $negative ? -$rounded : $rounded;
    }
}
