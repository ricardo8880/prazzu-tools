<?php

declare(strict_types=1);

namespace App\Core\Dates;

use App\Core\Exceptions\InvalidValue;
use DateTimeImmutable;
use DateTimeInterface;

final readonly class ReferenceDate
{
    private function __construct(private DateTimeImmutable $date) {}

    public static function fromString(string $date): self
    {
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $errors = DateTimeImmutable::getLastErrors();

        if ($parsed === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new InvalidValue('Data de referência inválida. Use o formato YYYY-MM-DD.');
        }

        return new self($parsed);
    }

    public static function fromDateTime(DateTimeInterface $date): self
    {
        return self::fromString($date->format('Y-m-d'));
    }

    public function value(): DateTimeImmutable
    {
        return $this->date;
    }

    public function toString(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function isBefore(self $other): bool
    {
        return $this->date < $other->date;
    }

    public function isAfter(self $other): bool
    {
        return $this->date > $other->date;
    }

    public function equals(self $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
