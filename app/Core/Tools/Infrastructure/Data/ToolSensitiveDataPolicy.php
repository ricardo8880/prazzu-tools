<?php

namespace App\Core\Tools\Infrastructure\Data;

use App\Core\Tools\Infrastructure\Enums\SensitiveDataMode;
use InvalidArgumentException;

final readonly class ToolSensitiveDataPolicy
{
    /** @param list<string> $fields */
    public function __construct(
        public SensitiveDataMode $mode = SensitiveDataMode::None,
        public array $fields = [],
    ) {
        if ($this->mode === SensitiveDataMode::None && $this->fields !== []) {
            throw new InvalidArgumentException('Política sem dados sensíveis não pode declarar campos.');
        }

        if ($this->mode !== SensitiveDataMode::None && $this->fields === []) {
            throw new InvalidArgumentException('Política de dados sensíveis exige campos explícitos.');
        }

        if (count($this->fields) !== count(array_unique($this->fields))) {
            throw new InvalidArgumentException('Campos sensíveis não podem se repetir.');
        }

        foreach ($this->fields as $field) {
            if (! is_string($field) || trim($field) === '') {
                throw new InvalidArgumentException('Campos sensíveis devem ser textos não vazios.');
            }
        }
    }

    public static function none(): self
    {
        return new self();
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            mode: SensitiveDataMode::from((string) ($data['mode'] ?? SensitiveDataMode::None->value)),
            fields: array_values($data['fields'] ?? []),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['mode' => $this->mode->value, 'fields' => $this->fields];
    }
}
