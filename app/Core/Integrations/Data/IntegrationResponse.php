<?php

namespace App\Core\Integrations\Data;

final readonly class IntegrationResponse
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public int $status,
        public array $data,
        public array $headers = [],
    ) {}

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
