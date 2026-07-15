<?php

namespace App\Core\Analytics\Domain\Events;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class AnalyticsEvent
{
    /** @param array<string, mixed> $properties */
    public function __construct(
        public string $name,
        public string $channel,
        public array $properties = [],
        public ?string $subjectType = null,
        public int|string|null $subjectId = null,
        public ?string $subjectSlug = null,
        public ?DateTimeInterface $occurredAt = null,
        public ?string $eventId = null,
    ) {
        if (! preg_match('/^[a-z][a-z0-9_.-]{1,79}$/', $name)) {
            throw new InvalidArgumentException("Nome de evento inválido: {$name}");
        }

        if (! preg_match('/^[a-z][a-z0-9_-]{1,39}$/', $channel)) {
            throw new InvalidArgumentException("Canal de Analytics inválido: {$channel}");
        }
    }

    /** @param array<string, mixed> $properties */
    public static function make(string $name, string $channel, array $properties = []): self
    {
        return new self(
            name: $name,
            channel: $channel,
            properties: $properties,
            subjectType: isset($properties['subject_type']) ? (string) $properties['subject_type'] : null,
            subjectId: $properties['subject_id'] ?? null,
            subjectSlug: isset($properties['subject_slug']) ? (string) $properties['subject_slug'] : null,
        );
    }

    public function identifier(): string
    {
        return $this->eventId ?? (string) Str::uuid();
    }

    public function date(): CarbonImmutable
    {
        return CarbonImmutable::instance($this->occurredAt ?? now());
    }
}
