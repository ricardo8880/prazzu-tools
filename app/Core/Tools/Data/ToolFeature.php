<?php

declare(strict_types=1);

namespace App\Core\Tools\Data;

use App\Core\Tools\Enums\ToolFeatureTier;
use InvalidArgumentException;

final readonly class ToolFeature
{
    public function __construct(
        public string $key,
        public string $name,
        public ToolFeatureTier $tier,
    ) {
        if (! preg_match('/^[a-z0-9]+(?:_[a-z0-9]+)*$/', $this->key)) {
            throw new InvalidArgumentException('A chave do recurso deve usar apenas letras minúsculas, números e sublinhados.');
        }

        if (trim($this->name) === '') {
            throw new InvalidArgumentException('O nome do recurso é obrigatório.');
        }
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            key: (string) ($data['key'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            tier: ToolFeatureTier::from((string) ($data['tier'] ?? '')),
        );
    }

    /** @return array{key: string, name: string, tier: string} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'tier' => $this->tier->value,
        ];
    }
}
