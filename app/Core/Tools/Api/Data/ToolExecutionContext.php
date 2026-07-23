<?php

namespace App\Core\Tools\Api\Data;

use App\Core\Tools\Api\Auth\ApiClient;

final readonly class ToolExecutionContext
{
    /** @param array<string, mixed> $metadata */
    public function __construct(
        public ApiClient $client,
        public ?string $userId = null,
        public array $metadata = [],
    ) {}
}
