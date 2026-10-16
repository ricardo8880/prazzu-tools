<?php

declare(strict_types=1);

namespace App\Core\Money;

use InvalidArgumentException;

final class AmericanMoneyInWords
{
    private const ONES = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
    ];

    private const TENS = [2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty', 6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'];

    public function convert(Money $money): string
    {
        if ($money->currency() !== Currency::USD || $money->minorAmount() <= 0) {
            throw new InvalidArgumentException('Amount-in-words conversion accepts positive U.S. dollar amounts only.');
        }

        $dollars = intdiv($money->minorAmount(), 100);
        $cents = $money->minorAmount() % 100;

        if ($dollars > 999_999_999) {
            throw new InvalidArgumentException('The maximum supported amount is $999,999,999.99.');
        }

        $parts = [];
        if ($dollars > 0) {
            $parts[] = $this->integer($dollars).' '.($dollars === 1 ? 'dollar' : 'dollars');
        }
        if ($cents > 0) {
            $parts[] = $this->integer($cents).' '.($cents === 1 ? 'cent' : 'cents');
        }

        return implode(' and ', $parts);
    }

    private function integer(int $number): string
    {
        if ($number < 20) {
            return self::ONES[$number];
        }

        if ($number < 100) {
            $tens = intdiv($number, 10);
            $ones = $number % 10;
            return self::TENS[$tens].($ones > 0 ? '-'.self::ONES[$ones] : '');
        }

        if ($number < 1_000) {
            $hundreds = intdiv($number, 100);
            $remainder = $number % 100;
            return self::ONES[$hundreds].' hundred'.($remainder > 0 ? ' '.$this->integer($remainder) : '');
        }

        foreach ([[1_000_000, 'million'], [1_000, 'thousand']] as [$divisor, $label]) {
            if ($number >= $divisor) {
                $quantity = intdiv($number, $divisor);
                $remainder = $number % $divisor;
                return $this->integer($quantity).' '.$label.($remainder > 0 ? ' '.$this->integer($remainder) : '');
            }
        }

        return self::ONES[0];
    }
}
