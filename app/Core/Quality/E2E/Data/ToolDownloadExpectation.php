<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Data;

use InvalidArgumentException;

final readonly class ToolDownloadExpectation
{
    /** @param list<string> $requiredEntries */
    public function __construct(
        public string $id,
        public string $testId,
        public string $format,
        public string $extension,
        public int $minimumBytes = 1,
        public ?string $filenameContains = null,
        public ?string $mimeType = null,
        public array $requiredEntries = [],
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->id)) {
            throw new InvalidArgumentException('O identificador do download E2E deve usar kebab-case.');
        }
        if (! in_array($this->format, ['pdf', 'xlsx', 'csv', 'docx', 'zip'], true)) {
            throw new InvalidArgumentException("Formato de download E2E inválido: {$this->format}.");
        }
        if (ltrim(strtolower($this->extension), '.') !== $this->format) {
            throw new InvalidArgumentException('A extensão do download deve corresponder ao formato declarado.');
        }
        if ($this->minimumBytes < 1) {
            throw new InvalidArgumentException('O tamanho mínimo do download deve ser positivo.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'test_id' => $this->testId,
            'format' => $this->format,
            'extension' => $this->extension,
            'minimum_bytes' => $this->minimumBytes,
            'filename_contains' => $this->filenameContains,
            'mime_type' => $this->mimeType,
            'required_entries' => $this->requiredEntries,
        ];
    }
}
