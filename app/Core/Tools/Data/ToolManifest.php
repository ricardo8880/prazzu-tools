<?php

namespace App\Core\Tools\Data;

use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\Infrastructure\Data\ToolExportPolicy;
use App\Core\Tools\Infrastructure\Data\ToolPersistencePolicy;
use App\Core\Tools\Infrastructure\Data\ToolSensitiveDataPolicy;
use App\Core\Tools\Infrastructure\Data\ToolSharingPolicy;
use InvalidArgumentException;

final readonly class ToolManifest
{
    /**
     * @param array<int, string> $keywords
     * @param array<int, ToolFeature> $features
     * @param array<int, ToolCapability> $capabilities
     */
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
        public array $features = [],
        public array $capabilities = [],
        public ?ToolPersistencePolicy $persistence = null,
        public ?ToolExportPolicy $export = null,
        public ?ToolSharingPolicy $sharing = null,
        public ?ToolSensitiveDataPolicy $sensitiveData = null,
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

        $capabilityValues = [];

        foreach ($this->capabilities as $capability) {
            if (! $capability instanceof ToolCapability) {
                throw new InvalidArgumentException('Todas as capacidades devem ser instâncias de ToolCapability.');
            }

            if (isset($capabilityValues[$capability->value])) {
                throw new InvalidArgumentException("A capacidade [{$capability->value}] está duplicada no manifesto.");
            }

            $capabilityValues[$capability->value] = true;
        }

        $featureKeys = [];

        foreach ($this->features as $feature) {
            if (! $feature instanceof ToolFeature) {
                throw new InvalidArgumentException('Todos os recursos da ferramenta devem ser instâncias de ToolFeature.');
            }

            if (isset($featureKeys[$feature->key])) {
                throw new InvalidArgumentException("O recurso [{$feature->key}] está duplicado no manifesto.");
            }

            $featureKeys[$feature->key] = true;
        }
    }

    public function hasCapability(ToolCapability $capability): bool
    {
        return in_array($capability, $this->capabilities, true);
    }

    public function feature(string $key): ?ToolFeature
    {
        foreach ($this->features as $feature) {
            if ($feature->key === $key) {
                return $feature;
            }
        }

        return null;
    }

    /** @return array<int, ToolFeature> */
    public function featuresFor(ToolFeatureTier $tier): array
    {
        return array_values(array_filter(
            $this->features,
            static fn (ToolFeature $feature): bool => $feature->tier === $tier,
        ));
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
            routeName: (string) ($data['route_name'] ?? ''),
            version: (string) ($data['version'] ?? '1.0.0'),
            access: ToolAccess::from((string) ($data['access'] ?? ToolAccess::Free->value)),
            status: ToolStatus::from((string) ($data['status'] ?? ToolStatus::Active->value)),
            position: (int) ($data['position'] ?? 1000),
            featured: (bool) ($data['featured'] ?? false),
            supportsHistory: (bool) ($data['supports_history'] ?? false),
            storesSensitiveData: (bool) ($data['stores_sensitive_data'] ?? false),
            keywords: array_values($data['keywords'] ?? []),
            features: array_map(
                static fn (array $feature): ToolFeature => ToolFeature::fromArray($feature),
                array_values($data['features'] ?? []),
            ),
            capabilities: array_map(
                static fn (string $capability): ToolCapability => ToolCapability::from($capability),
                array_values($data['capabilities'] ?? []),
            ),
            persistence: isset($data['persistence']) ? ToolPersistencePolicy::fromArray($data['persistence']) : null,
            export: isset($data['export']) ? ToolExportPolicy::fromArray($data['export']) : null,
            sharing: isset($data['sharing']) ? ToolSharingPolicy::fromArray($data['sharing']) : null,
            sensitiveData: isset($data['sensitive_data']) ? ToolSensitiveDataPolicy::fromArray($data['sensitive_data']) : null,
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
            'features' => array_map(
                static fn (ToolFeature $feature): array => $feature->toArray(),
                $this->features,
            ),
            'capabilities' => array_map(
                static fn (ToolCapability $capability): string => $capability->value,
                $this->capabilities,
            ),
            'persistence' => $this->persistence?->toArray(),
            'export' => $this->export?->toArray(),
            'sharing' => $this->sharing?->toArray(),
            'sensitive_data' => $this->sensitiveData?->toArray(),
        ];
    }
}
