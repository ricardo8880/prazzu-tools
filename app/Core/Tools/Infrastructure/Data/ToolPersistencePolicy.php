<?php

namespace App\Core\Tools\Infrastructure\Data;

use InvalidArgumentException;

final readonly class ToolPersistencePolicy
{
    public function __construct(
        public bool $enabled = false,
        public int $schemaVersion = 1,
        public ?int $retentionDays = null,
        public int $minimumReadableSchemaVersion = 1,
    ) {
        if ($this->schemaVersion < 1) {
            throw new InvalidArgumentException('A versão do schema deve ser maior ou igual a 1.');
        }

        if ($this->minimumReadableSchemaVersion < 1 || $this->minimumReadableSchemaVersion > $this->schemaVersion) {
            throw new InvalidArgumentException('A versão mínima legível deve estar entre 1 e a versão atual do schema.');
        }

        if ($this->enabled && ($this->retentionDays === null || $this->retentionDays < 1)) {
            throw new InvalidArgumentException('Persistência ativa exige retenção mínima de um dia.');
        }

        if (! $this->enabled && $this->retentionDays !== null) {
            throw new InvalidArgumentException('Persistência desabilitada não pode definir retenção.');
        }
    }

    public static function disabled(): self
    {
        return new self;
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            enabled: (bool) ($data['enabled'] ?? false),
            schemaVersion: (int) ($data['schema_version'] ?? 1),
            retentionDays: isset($data['retention_days']) ? (int) $data['retention_days'] : null,
            minimumReadableSchemaVersion: (int) ($data['minimum_readable_schema_version'] ?? 1),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'schema_version' => $this->schemaVersion,
            'retention_days' => $this->retentionDays,
            'minimum_readable_schema_version' => $this->minimumReadableSchemaVersion,
        ];
    }
}
