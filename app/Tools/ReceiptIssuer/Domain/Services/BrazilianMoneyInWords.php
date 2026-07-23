<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Money\Currency;
use App\Core\Money\Money;
use InvalidArgumentException;

final class BrazilianMoneyInWords
{
    private const UNITS = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];
    private const TEENS = [10 => 'dez', 11 => 'onze', 12 => 'doze', 13 => 'treze', 14 => 'quatorze', 15 => 'quinze', 16 => 'dezesseis', 17 => 'dezessete', 18 => 'dezoito', 19 => 'dezenove'];
    private const TENS = [2 => 'vinte', 3 => 'trinta', 4 => 'quarenta', 5 => 'cinquenta', 6 => 'sessenta', 7 => 'setenta', 8 => 'oitenta', 9 => 'noventa'];
    private const HUNDREDS = [1 => 'cento', 2 => 'duzentos', 3 => 'trezentos', 4 => 'quatrocentos', 5 => 'quinhentos', 6 => 'seiscentos', 7 => 'setecentos', 8 => 'oitocentos', 9 => 'novecentos'];

    public function convert(Money $money): string
    {
        if ($money->currency() !== Currency::BRL || $money->minorAmount() <= 0) {
            throw new InvalidArgumentException('A conversão por extenso aceita apenas valores positivos em reais.');
        }

        $reais = intdiv($money->minorAmount(), 100);
        $centavos = $money->minorAmount() % 100;

        if ($reais > 999_999_999) {
            throw new InvalidArgumentException('O valor máximo por extenso é R$ 999.999.999,99.');
        }

        $parts = [];
        if ($reais > 0) {
            $currencyLabel = $reais === 1 ? 'real' : 'reais';
            $connector = $reais >= 1_000_000 && $reais % 1_000_000 === 0 ? ' de ' : ' ';
            $parts[] = $this->integer($reais).$connector.$currencyLabel;
        }
        if ($centavos > 0) {
            $parts[] = $this->integer($centavos).' '.($centavos === 1 ? 'centavo' : 'centavos');
        }

        return implode(' e ', $parts);
    }

    private function integer(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $groups = [
            [1_000_000, 'milhão', 'milhões'],
            [1_000, 'mil', 'mil'],
        ];

        $parts = [];
        $remainder = $number;

        foreach ($groups as [$divisor, $singular, $plural]) {
            $quantity = intdiv($remainder, $divisor);
            if ($quantity === 0) {
                continue;
            }

            $parts[] = $divisor === 1_000 && $quantity === 1
                ? 'mil'
                : $this->underThousand($quantity).' '.($quantity === 1 ? $singular : $plural);
            $remainder %= $divisor;
        }

        if ($remainder > 0) {
            $parts[] = $this->underThousand($remainder);
        }

        return $this->joinGroups($parts, $number);
    }

    private function underThousand(int $number): string
    {
        if ($number === 100) {
            return 'cem';
        }

        $parts = [];
        $hundreds = intdiv($number, 100);
        $remainder = $number % 100;

        if ($hundreds > 0) {
            $parts[] = self::HUNDREDS[$hundreds];
        }
        if ($remainder >= 10 && $remainder <= 19) {
            $parts[] = self::TEENS[$remainder];
        } else {
            $tens = intdiv($remainder, 10);
            $unit = $remainder % 10;
            if ($tens >= 2) {
                $parts[] = self::TENS[$tens];
            }
            if ($unit > 0) {
                $parts[] = self::UNITS[$unit];
            }
        }

        return implode(' e ', $parts);
    }

    /** @param list<string> $parts */
    private function joinGroups(array $parts, int $number): string
    {
        if (count($parts) <= 1) {
            return $parts[0] ?? '';
        }

        $lastGroup = $number % 1000;
        $separator = ($lastGroup > 0 && ($lastGroup < 100 || $lastGroup % 100 === 0)) ? ' e ' : ' ';

        $last = array_pop($parts);
        return implode(' ', $parts).$separator.$last;
    }
}
