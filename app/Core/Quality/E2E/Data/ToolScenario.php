<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Data;

use InvalidArgumentException;

final readonly class ToolScenario
{
    /**
     * @param list<array<string, mixed>> $steps
     * @param list<array<string, mixed>> $expectations
     * @param list<string> $tags
     * @param list<ToolDownloadExpectation> $downloads
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $kind,
        public string $toolSlug,
        public array $steps,
        public array $expectations,
        public array $tags = [],
        public string $accessProfile = 'visitor',
        public array $downloads = [],
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->id)) {
            throw new InvalidArgumentException('O identificador do cenário E2E deve usar kebab-case.');
        }
        if (! in_array($this->kind, ['valid', 'invalid', 'boundary'], true)) {
            throw new InvalidArgumentException("Tipo de cenário E2E inválido: {$this->kind}.");
        }
        if ($this->steps === [] || $this->expectations === []) {
            throw new InvalidArgumentException('Cenários E2E precisam declarar etapas e expectativas.');
        }
        foreach ($this->downloads as $download) {
            if (! $download instanceof ToolDownloadExpectation) {
                throw new InvalidArgumentException('Downloads E2E precisam usar ToolDownloadExpectation.');
            }
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'kind' => $this->kind,
            'tool_slug' => $this->toolSlug,
            'access_profile' => $this->accessProfile,
            'tags' => $this->tags,
            'steps' => $this->steps,
            'expectations' => $this->expectations,
            'downloads' => array_map(
                static fn (ToolDownloadExpectation $download): array => $download->toArray(),
                $this->downloads,
            ),
        ];
    }
}
