<?php

namespace App\Core\ToolIntegration\Services;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Core\ToolIntegration\Data\IntegrationContract;
use InvalidArgumentException;

final class InMemoryToolIntegrationCatalog implements ToolIntegrationCatalog
{
    /** @var array<string, IntegrationContract> */
    private array $contracts = [];

    public function register(IntegrationContract $contract): void
    {
        if (isset($this->contracts[$contract->key()])) {
            throw new InvalidArgumentException("O contrato [{$contract->key()}] já está registrado.");
        }

        $this->contracts[$contract->key()] = $contract;
    }

    public function find(string $name, int $version): ?IntegrationContract
    {
        return $this->contracts["{$name}:v{$version}"] ?? null;
    }

    public function all(): array
    {
        return $this->contracts;
    }
}
