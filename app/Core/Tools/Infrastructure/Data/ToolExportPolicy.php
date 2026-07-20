<?php

namespace App\Core\Tools\Infrastructure\Data;

use InvalidArgumentException;

final readonly class ToolExportPolicy
{
    private const ALLOWED_FORMATS = ['csv', 'xlsx', 'pdf', 'json', 'print'];

    /** @param list<string> $formats */
    public function __construct(
        public bool $enabled = false,
        public array $formats = [],
    ) {
        if ($this->enabled && $this->formats === []) {
            throw new InvalidArgumentException('Exportação ativa exige ao menos um formato.');
        }

        if (! $this->enabled && $this->formats !== []) {
            throw new InvalidArgumentException('Exportação desabilitada não pode declarar formatos.');
        }

        if (count($this->formats) !== count(array_unique($this->formats))) {
            throw new InvalidArgumentException('Formatos de exportação não podem se repetir.');
        }

        foreach ($this->formats as $format) {
            if (! in_array($format, self::ALLOWED_FORMATS, true)) {
                throw new InvalidArgumentException("Formato de exportação [{$format}] não suportado.");
            }
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
            formats: array_values($data['formats'] ?? []),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['enabled' => $this->enabled, 'formats' => $this->formats];
    }
}
