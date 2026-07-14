<?php

declare(strict_types=1);

namespace App\Core\Imports\Data;

final readonly class TabularDataset
{
    /**
     * @param list<string> $headers
     * @param list<array<string, string|null>> $rows
     */
    public function __construct(
        public array $headers,
        public array $rows,
        public string $originalName,
        public string $format,
    ) {}

    public function toArray(): array
    {
        return [
            'headers' => $this->headers,
            'rows' => $this->rows,
            'original_name' => $this->originalName,
            'format' => $this->format,
            'total_rows' => count($this->rows),
        ];
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            headers: array_values($payload['headers'] ?? []),
            rows: array_values($payload['rows'] ?? []),
            originalName: (string) ($payload['original_name'] ?? ''),
            format: (string) ($payload['format'] ?? ''),
        );
    }
}
