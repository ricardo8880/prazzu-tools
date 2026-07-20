<?php

namespace App\Core\ToolIntegration\Data;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class IntegrationPayload
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $sourceTool,
        public string $contractName,
        public int $contractVersion,
        public array $data,
        public DateTimeImmutable $createdAt = new DateTimeImmutable,
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->sourceTool)) {
            throw new InvalidArgumentException('A ferramenta de origem deve usar um slug válido.');
        }

        if ($this->contractVersion < 1) {
            throw new InvalidArgumentException('A versão do contrato deve ser maior que zero.');
        }
    }

    public function contractKey(): string
    {
        return "{$this->contractName}:v{$this->contractVersion}";
    }
}
