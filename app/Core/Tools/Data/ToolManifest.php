<?php

namespace App\Core\Tools\Data;

use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use InvalidArgumentException;

final readonly class ToolManifest
{
    /** @param array<int, string> $keywords */
    public function __construct(
        public string $slug,
        public string $name,
        public string $description,
        public ToolCategory $category,
        public string $icon,
        public string $routeName,
        public string $version = '1.0.0',
        public ToolAccess $access = ToolAccess::Free,
        public ToolStatus $status = ToolStatus::Active,
        public int $position = 1000,
        public bool $featured = false,
        public bool $supportsHistory = false,
        public bool $storesSensitiveData = false,
        public array $keywords = [],
    ) {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->slug)) {
            throw new InvalidArgumentException('O slug da ferramenta deve usar apenas letras minúsculas, números e hífens.');
        }

        if (trim($this->name) === '' || trim($this->description) === '') {
            throw new InvalidArgumentException('Nome e descrição da ferramenta são obrigatórios.');
        }

        if (! preg_match('/^tools\.[a-z0-9.-]+$/', $this->routeName)) {
            throw new InvalidArgumentException('A rota principal da ferramenta deve usar o prefixo [tools.].');
        }

        if (! preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $this->version)) {
            throw new InvalidArgumentException('A versão da ferramenta deve seguir versionamento semântico.');
        }

        if ($this->position < 0) {
            throw new InvalidArgumentException('A posição da ferramenta não pode ser negativa.');
        }

        foreach ($this->keywords as $keyword) {
            if (! is_string($keyword) || trim($keyword) === '') {
                throw new InvalidArgumentException('Todas as palavras-chave devem ser textos não vazios.');
            }
        }
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: (string) ($data['slug'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            category: ToolCategory::from((string) ($data['category'] ?? '')),
            icon: (string) ($data['icon'] ?? ''),
            routeName: (string) ($data['route_name'] ?? 'tools.show'),
            version: (string) ($data['version'] ?? '0.0.0-placeholder'),
            access: ToolAccess::from((string) ($data['access'] ?? ToolAccess::Free->value)),
            status: ToolStatus::from((string) ($data['status'] ?? ToolStatus::Active->value)),
            position: (int) ($data['position'] ?? 1000),
            featured: (bool) ($data['featured'] ?? false),
            supportsHistory: (bool) ($data['supports_history'] ?? false),
            storesSensitiveData: (bool) ($data['stores_sensitive_data'] ?? false),
            keywords: array_values($data['keywords'] ?? []),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category->value,
            'icon' => $this->icon,
            'route_name' => $this->routeName,
            'version' => $this->version,
            'access' => $this->access->value,
            'status' => $this->status->value,
            'position' => $this->position,
            'is_featured' => $this->featured,
            'supports_history' => $this->supportsHistory,
            'stores_sensitive_data' => $this->storesSensitiveData,
            'keywords' => $this->keywords,
        ];
    }
}
