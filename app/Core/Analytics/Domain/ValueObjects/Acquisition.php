<?php

namespace App\Core\Analytics\Domain\ValueObjects;

final readonly class Acquisition
{
    /** @param array<string, string|null> $utm */
    public function __construct(
        public string $source,
        public string $medium,
        public ?string $campaign = null,
        public array $utm = [],
        public ?string $referrerHost = null,
    ) {}

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'medium' => $this->medium,
            'campaign' => $this->campaign,
            'utm_source' => $this->utm['source'] ?? null,
            'utm_medium' => $this->utm['medium'] ?? null,
            'utm_campaign' => $this->utm['campaign'] ?? null,
            'utm_term' => $this->utm['term'] ?? null,
            'utm_content' => $this->utm['content'] ?? null,
        ];
    }
}
