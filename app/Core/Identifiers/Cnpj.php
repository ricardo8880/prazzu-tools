<?php

declare(strict_types=1);

namespace App\Core\Identifiers;

use App\Core\Exceptions\InvalidValue;

final readonly class Cnpj
{
    private const FIRST_WEIGHTS = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    private const SECOND_WEIGHTS = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    private function __construct(private string $digits)
    {
    }

    public static function fromString(string $value): self
    {
        $digits = Digits::only($value);

        if (! self::isValid($digits)) {
            throw new InvalidValue('CNPJ inválido.');
        }

        return new self($digits);
    }

    public static function isValid(string $value): bool
    {
        $digits = Digits::only($value);

        if (strlen($digits) !== 14 || Digits::hasAllEqual($digits)) {
            return false;
        }

        return self::calculateDigit(substr($digits, 0, 12), self::FIRST_WEIGHTS) === (int) $digits[12]
            && self::calculateDigit(substr($digits, 0, 13), self::SECOND_WEIGHTS) === (int) $digits[13];
    }

    public function digits(): string
    {
        return $this->digits;
    }

    public function formatted(): string
    {
        return substr($this->digits, 0, 2).'.'
            .substr($this->digits, 2, 3).'.'
            .substr($this->digits, 5, 3).'/'
            .substr($this->digits, 8, 4).'-'
            .substr($this->digits, 12, 2);
    }

    public function masked(): string
    {
        return '**.'.substr($this->digits, 2, 3).'.'.substr($this->digits, 5, 3).'/****-**';
    }

    public function equals(self $other): bool
    {
        return $this->digits === $other->digits;
    }

    /** @param list<int> $weights */
    private static function calculateDigit(string $base, array $weights): int
    {
        $sum = 0;

        foreach ($weights as $index => $weight) {
            $sum += ((int) $base[$index]) * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
