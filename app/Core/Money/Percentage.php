<?php

declare(strict_types=1);

namespace App\Core\Money;

use App\Core\Exceptions\InvalidValue;

final readonly class Percentage
{
    private const SCALE = 6;
    private const FACTOR = 1_000_000;

    private function __construct(private int $millionthsOfPercent)
    {
    }

    public static function fromString(string $value): self
    {
        $normalized = trim(str_replace(',', '.', $value));

        if (! preg_match('/^([+-]?)(\d+)(?:\.(\d{1,6}))?$/', $normalized, $matches)) {
            throw new InvalidValue('Percentual inválido. Use até seis casas decimais.');
        }

        $whole = (int) $matches[2];
        $fraction = str_pad($matches[3] ?? '', self::SCALE, '0');
        $scaled = self::checkedAdd(
            self::checkedMultiply($whole, self::FACTOR),
            (int) $fraction,
        );

        return new self(($matches[1] ?? '') === '-' ? -$scaled : $scaled);
    }

    public static function fromBasisPoints(int $basisPoints): self
    {
        return new self(self::checkedMultiply($basisPoints, 10_000));
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function millionthsOfPercent(): int
    {
        return $this->millionthsOfPercent;
    }

    public function numerator(): int
    {
        return $this->millionthsOfPercent;
    }

    public function denominator(): int
    {
        return 100 * self::FACTOR;
    }

    public function toDecimalString(bool $trimTrailingZeros = true): string
    {
        $negative = $this->millionthsOfPercent < 0;
        $absolute = abs($this->millionthsOfPercent);
        $whole = intdiv($absolute, self::FACTOR);
        $fraction = str_pad((string) ($absolute % self::FACTOR), self::SCALE, '0', STR_PAD_LEFT);

        if ($trimTrailingZeros) {
            $fraction = rtrim($fraction, '0');
        }

        $formatted = $fraction === '' ? (string) $whole : $whole.'.'.$fraction;

        return ($negative ? '-' : '').$formatted;
    }

    public function equals(self $other): bool
    {
        return $this->millionthsOfPercent === $other->millionthsOfPercent;
    }

    private static function checkedMultiply(int $left, int $right): int
    {
        if ($left !== 0 && abs($left) > intdiv(PHP_INT_MAX, abs($right))) {
            throw new InvalidValue('Percentual fora do intervalo suportado.');
        }

        return $left * $right;
    }

    private static function checkedAdd(int $left, int $right): int
    {
        if ($right > 0 && $left > PHP_INT_MAX - $right) {
            throw new InvalidValue('Percentual fora do intervalo suportado.');
        }

        return $left + $right;
    }
}
