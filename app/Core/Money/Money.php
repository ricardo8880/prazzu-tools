<?php

declare(strict_types=1);

namespace App\Core\Money;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;

final readonly class Money
{
    private function __construct(
        private int $minorAmount,
        private Currency $currency,
    ) {}

    public static function fromMinor(int $minorAmount, Currency $currency = Currency::USD): self
    {
        return new self($minorAmount, $currency);
    }

    public static function fromDecimal(string $amount, Currency $currency = Currency::USD): self
    {
        $normalized = trim(str_ireplace(['USD', '$', ' '], '', $amount));
        $normalized = str_replace(',', '', $normalized);
        $digits = $currency->fractionDigits();
        $pattern = '/^([+-]?)(\d+)(?:\.(\d{1,'.$digits.'}))?$/';

        if (! preg_match($pattern, $normalized, $matches)) {
            throw new InvalidValue("Invalid monetary amount for {$currency->value}.");
        }

        $factor = 10 ** $digits;
        $whole = (int) $matches[2];

        if ($whole > intdiv(PHP_INT_MAX, $factor)) {
            throw new InvalidValue('Monetary amount is outside the supported range.');
        }

        $fraction = str_pad($matches[3] ?? '', $digits, '0');
        $minor = ($whole * $factor) + (int) $fraction;

        return new self(($matches[1] ?? '') === '-' ? -$minor : $minor, $currency);
    }

    public static function zero(Currency $currency = Currency::USD): self
    {
        return new self(0, $currency);
    }

    public function minorAmount(): int
    {
        return $this->minorAmount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        if (($other->minorAmount > 0 && $this->minorAmount > PHP_INT_MAX - $other->minorAmount)
            || ($other->minorAmount < 0 && $this->minorAmount < PHP_INT_MIN - $other->minorAmount)) {
            throw new InvalidValue('Monetary result is outside the supported range.');
        }

        return new self($this->minorAmount + $other->minorAmount, $this->currency);
    }

    public function subtract(self $other): self
    {
        return $this->add($other->negate());
    }

    public function negate(): self
    {
        if ($this->minorAmount === PHP_INT_MIN) {
            throw new InvalidValue('Monetary amount is outside the supported range.');
        }

        return new self(-$this->minorAmount, $this->currency);
    }

    public function percentage(Percentage $percentage, RoundingMode $rounding = RoundingMode::HalfUp): self
    {
        $numerator = $this->checkedMultiply($this->minorAmount, $percentage->numerator());

        return new self(
            IntegerRounding::divide($numerator, $percentage->denominator(), $rounding),
            $this->currency,
        );
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->checkedMultiply($this->minorAmount, $multiplier), $this->currency);
    }

    public function divide(int $divisor, RoundingMode $rounding = RoundingMode::HalfUp): self
    {
        return new self(IntegerRounding::divide($this->minorAmount, $divisor, $rounding), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->minorAmount === $other->minorAmount;
    }

    public function formatEnUs(): string
    {
        $digits = $this->currency->fractionDigits();
        $factor = 10 ** $digits;
        $absolute = abs($this->minorAmount);
        $whole = intdiv($absolute, $factor);
        $fraction = str_pad((string) ($absolute % $factor), $digits, '0', STR_PAD_LEFT);
        $formattedWhole = number_format($whole, 0, '.', ',');

        return ($this->minorAmount < 0 ? '-' : '').$this->currency->symbol().$formattedWhole.'.'.$fraction;
    }

    /** @deprecated Use formatEnUs(). Kept temporarily for backward-compatible persisted exporters. */
    public function formatPtBr(): string
    {
        return $this->formatEnUs();
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatch('Monetary operations require the same currency.');
        }
    }

    private function checkedMultiply(int $left, int $right): int
    {
        if ($left !== 0 && $right !== 0 && abs($left) > intdiv(PHP_INT_MAX, abs($right))) {
            throw new InvalidValue('Monetary result is outside the supported range.');
        }

        return $left * $right;
    }
}
