<?php

namespace App\Core\ToolIntegration\Contracts;

use App\Core\ToolIntegration\Data\IntegrationPayload;

interface ToolResultResolver
{
    public function latest(string $contractName, int $contractVersion): ?IntegrationPayload;
}
