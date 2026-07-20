<?php

namespace App\Core\ToolIntegration\Contracts;

use App\Core\ToolIntegration\Data\IntegrationContract;

interface ToolIntegrationCatalog
{
    public function register(IntegrationContract $contract): void;

    public function find(string $name, int $version): ?IntegrationContract;

    /** @return array<string, IntegrationContract> */
    public function all(): array;
}
