<?php

declare(strict_types=1);

namespace App\Core\Dates;

use App\Core\Exceptions\InvalidValue;

final readonly class EffectivePeriod
{
    public function __construct(
        public ReferenceDate $startsAt,
        public ?ReferenceDate $endsAt = null,
    ) {
        if ($endsAt?->isBefore($startsAt)) {
            throw new InvalidValue('O fim da vigência não pode ser anterior ao início.');
        }
    }

    public static function from(string $startsAt, ?string $endsAt = null): self
    {
        return new self(
            ReferenceDate::fromString($startsAt),
            $endsAt === null ? null : ReferenceDate::fromString($endsAt),
        );
    }

    public function contains(ReferenceDate $date): bool
    {
        return ! $date->isBefore($this->startsAt)
            && ($this->endsAt === null || ! $date->isAfter($this->endsAt));
    }

    public function overlaps(self $other): bool
    {
        if ($this->endsAt !== null && $this->endsAt->isBefore($other->startsAt)) {
            return false;
        }

        return $other->endsAt === null || ! $other->endsAt->isBefore($this->startsAt);
    }
}
