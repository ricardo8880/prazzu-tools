<?php

namespace App\Core\ToolIntegration\Contracts;

use App\Core\ToolIntegration\Data\IntegrationPayload;

interface ToolResultPublisher
{
    public function publish(IntegrationPayload $payload): void;
}
