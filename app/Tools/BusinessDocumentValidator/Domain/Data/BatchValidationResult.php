<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class BatchValidationResult
{
    /** @param list<array<string, mixed>> $rows */
    public function __construct(
        public array $rows,
        public int $valid,
        public int $invalid,
        public int $duplicates,
        public int $withInconsistencies,
        public int $registryConsulted,
        public int $registryUnavailable,
    ) {}

    public function toArray(): array
    {
        return [
            'rows' => $this->rows,
            'summary' => [
                'total' => count($this->rows),
                'valid' => $this->valid,
                'invalid' => $this->invalid,
                'duplicates' => $this->duplicates,
                'with_inconsistencies' => $this->withInconsistencies,
                'registry_consulted' => $this->registryConsulted,
                'registry_unavailable' => $this->registryUnavailable,
            ],
        ];
    }
}
