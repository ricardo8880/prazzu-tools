<?php

namespace App\Core\Analytics\Domain\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final readonly class AnalyticsPeriod
{
    public function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {
        if ($start->isAfter($end)) {
            throw new InvalidArgumentException('A data inicial não pode ser posterior à data final.');
        }

        if ($start->diffInDays($end) > 365) {
            throw new InvalidArgumentException('O período máximo do dashboard é de 366 dias.');
        }
    }

    public static function lastDays(int $days): self
    {
        $days = max(1, min($days, 366));
        $end = CarbonImmutable::today()->endOfDay();

        return new self($end->startOfDay()->subDays($days - 1), $end);
    }

    public static function between(string $start, string $end): self
    {
        return new self(
            CarbonImmutable::parse($start)->startOfDay(),
            CarbonImmutable::parse($end)->endOfDay(),
        );
    }

    public function previous(): self
    {
        $days = $this->days();
        $end = $this->start->subDay()->endOfDay();

        return new self($end->startOfDay()->subDays($days - 1), $end);
    }

    public function days(): int
    {
        return $this->start->startOfDay()->diffInDays($this->end->startOfDay()) + 1;
    }

    public function label(): string
    {
        if ($this->days() === 1) {
            return $this->start->format('d/m/Y');
        }

        return $this->start->format('d/m/Y').' a '.$this->end->format('d/m/Y');
    }
}
