<?php

namespace App\Core\Tools;

use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\Enums\ToolAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ToolCatalog
{
    public function __construct(private readonly ToolRegistry $registry) {}

    /** @return Collection<int, array<string, mixed>> */
    public function all(bool $onlyCatalogVisible = true): Collection
    {
        return collect($this->registry->manifests($onlyCatalogVisible))
            ->map(fn (ToolManifest $tool): array => $this->present($tool))
            ->sortBy('position')
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function byCategory(string $category): Collection
    {
        return $this->all()->where('category', $category)->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function withCapability(ToolCapability $capability): Collection
    {
        return $this->all()->filter(
            static fn (array $tool): bool => in_array($capability->value, $tool['capabilities'], true),
        )->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function byStatus(ToolStatus $status): Collection
    {
        return $this->all(false)->where('status', $status->value)->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function byAccess(ToolAccess $access): Collection
    {
        return $this->all(false)->where('access', $access->value)->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function featured(): Collection
    {
        return $this->all()
            ->filter(static fn (array $tool): bool => (bool) $tool['is_featured'])
            ->values();
    }

    /** @return array<string, mixed>|null */
    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function search(?string $query = null, ?string $category = null): Collection
    {
        $tools = $this->all();

        if ($category !== null && $category !== 'todas') {
            $tools = $this->byCategory($category);
        }

        $query = trim((string) $query);

        if ($query === '') {
            return $tools->values();
        }

        $needle = Str::lower(Str::ascii($query));

        return $tools->filter(function (array $tool) use ($needle): bool {
            $haystack = Str::lower(Str::ascii(implode(' ', [
                $tool['name'],
                $tool['description'],
                $tool['category'],
                implode(' ', $tool['keywords']),
            ])));

            return str_contains($haystack, $needle);
        })->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function categories(bool $includeAll = true): Collection
    {
        $counts = $this->all()->countBy('category');

        $categories = collect(config('tools.categories', []))
            ->map(static fn (array $category, string $slug): array => [
                'slug' => $slug,
                'name' => $category['name'],
                'icon' => $category['icon'],
                'count' => (int) $counts->get($slug, 0),
                'url' => route('tools.category', ['category' => $slug]),
            ])
            ->values();

        if (! $includeAll) {
            return $categories;
        }

        return $categories->prepend([
            'slug' => 'todas',
            'name' => 'Todos',
            'icon' => 'bi-grid',
            'count' => $this->all()->count(),
            'url' => route('tools.index'),
        ])->values();
    }

    /** @return array<string, mixed> */
    private function present(ToolManifest $manifest): array
    {
        $essentialFeatures = array_map(
            static fn (ToolFeature $feature): array => $feature->toArray(),
            $manifest->featuresFor(ToolFeatureTier::Essential),
        );
        $plusFeatures = array_map(
            static fn (ToolFeature $feature): array => $feature->toArray(),
            $manifest->featuresFor(ToolFeatureTier::Plus),
        );
        $hasPlusFeatures = $plusFeatures !== [];

        return array_merge($manifest->toArray(), [
            'is_active' => $manifest->status->acceptsNewExecutions(),
            'essential_features' => $essentialFeatures,
            'plus_features' => $plusFeatures,
            'has_plus_features' => $hasPlusFeatures,
            'capability_labels' => array_map(
                static fn (ToolCapability $capability): string => $capability->label(),
                $manifest->capabilities,
            ),
            'tone' => 'purple',
            'badge' => $hasPlusFeatures ? 'Grátis + Plus' : 'Grátis',
            'badge_tone' => $hasPlusFeatures ? 'purple' : 'green',
        ]);
    }
}
