<?php

declare(strict_types=1);

namespace App\Core\Identifiers;

use App\Core\Exceptions\InvalidValue;

final readonly class Cpf
{
    private function __construct(private string $digits) {}

    public static function fromString(string $value): self
    {
        $digits = Digits::only($value);

        if (! self::isValid($digits)) {
            throw new InvalidValue('CPF inválido.');
        }

        return new self($digits);
    }

    public static function isValid(string $value): bool
    {
        $digits = Digits::only($value);

        if (strlen($digits) !== 11 || Digits::hasAllEqual($digits)) {
            return false;
        }

        for ($position = 9; $position <= 10; $position++) {
            $sum = 0;

            for ($index = 0; $index < $position; $index++) {
                $sum += ((int) $digits[$index]) * (($position + 1) - $index);
            }

            $checkDigit = ($sum * 10) % 11;
            $checkDigit = $checkDigit === 10 ? 0 : $checkDigit;

            if ($checkDigit !== (int) $digits[$position]) {
                return false;
            }
        }

        return true;
    }

    public function digits(): string
    {
        return $this->digits;
    }

    public function formatted(): string
    {
        return substr($this->digits, 0, 3).'.'
            .substr($this->digits, 3, 3).'.'
            .substr($this->digits, 6, 3).'-'
            .substr($this->digits, 9, 2);
    }

    public function masked(): string
    {
        return '***.'.substr($this->digits, 3, 3).'.'.substr($this->digits, 6, 3).'-**';
    }

    public function equals(self $other): bool
    {
        return $this->digits === $other->digits;
    }
}
