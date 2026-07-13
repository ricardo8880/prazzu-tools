<?php

namespace App\Core\Integrations\Contracts;

use App\Core\Integrations\Data\IntegrationResponse;

interface ExternalServiceClient
{
    /** @param array<string, mixed> $options */
    public function request(string $method, string $uri, array $options = []): IntegrationResponse;
}
