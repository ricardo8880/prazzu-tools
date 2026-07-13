<?php

declare(strict_types=1);

namespace App\Core\Dates;

use App\Core\Exceptions\InvalidValue;

final readonly class DatePeriod
{
    public function __construct(
        public ReferenceDate $start,
        public ReferenceDate $end,
    ) {
        if ($end->isBefore($start)) {
            throw new InvalidValue('O fim do período não pode ser anterior ao início.');
        }
    }

    public function contains(ReferenceDate $date): bool
    {
        return ! $date->isBefore($this->start) && ! $date->isAfter($this->end);
    }

    public function overlaps(self $other): bool
    {
        return ! $this->end->isBefore($other->start) && ! $other->end->isBefore($this->start);
    }
}
