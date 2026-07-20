<?php

namespace App\Core\ToolIntegration\Contracts;

use App\Core\ToolIntegration\Data\IntegrationPayload;

interface ToolResultStore
{
    public function put(IntegrationPayload $payload): void;

    public function latest(string $contractKey): ?IntegrationPayload;
}
