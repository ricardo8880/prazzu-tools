<?php

declare(strict_types=1);

namespace App\Core\Dates;

use App\Core\Exceptions\InvalidValue;

final readonly class Competence
{
    public function __construct(
        public int $year,
        public int $month,
    ) {
        if ($year < 1900 || $year > 9999) {
            throw new InvalidValue('Ano de competência inválido.');
        }

        if ($month < 1 || $month > 12) {
            throw new InvalidValue('Mês de competência inválido.');
        }
    }

    public static function fromString(string $competence): self
    {
        if (! preg_match('/^(\d{4})-(\d{2})$/', $competence, $matches)) {
            throw new InvalidValue('Competência inválida. Use o formato YYYY-MM.');
        }

        return new self((int) $matches[1], (int) $matches[2]);
    }

    public static function fromReferenceDate(ReferenceDate $date): self
    {
        return new self(
            (int) $date->value()->format('Y'),
            (int) $date->value()->format('m'),
        );
    }

    public function firstDay(): ReferenceDate
    {
        return ReferenceDate::fromString($this->toString().'-01');
    }

    public function lastDay(): ReferenceDate
    {
        return ReferenceDate::fromString($this->firstDay()->value()->modify('last day of this month')->format('Y-m-d'));
    }

    public function toString(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }

    public function equals(self $other): bool
    {
        return $this->year === $other->year && $this->month === $other->month;
    }
}
