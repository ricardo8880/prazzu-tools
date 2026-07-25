<?php

namespace App\Core\Tools;

use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
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

    /** @return Collection<int, array<string, mixed>> */
    public function latest(int $limit = 8): Collection
    {
        return $this->all()
            ->sortByDesc('position')
            ->take(max(0, $limit))
            ->values();
    }

    /** @return array<string, mixed>|null */
    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function related(string $slug, int $limit = 4): Collection
    {
        $current = $this->find($slug);

        if ($current === null || $limit < 1) {
            return collect();
        }

        $keywords = collect($current['keywords'])
            ->map(static fn (string $keyword): string => Str::lower(Str::ascii($keyword)))
            ->all();

        return $this->all()
            ->reject(static fn (array $tool): bool => $tool['slug'] === $slug)
            ->map(function (array $tool) use ($current, $keywords): array {
                $candidateKeywords = array_map(
                    static fn (string $keyword): string => Str::lower(Str::ascii($keyword)),
                    $tool['keywords'],
                );
                $sharedKeywords = count(array_intersect($keywords, $candidateKeywords));
                $sameCategory = $tool['category'] === $current['category'];

                return [
                    ...$tool,
                    '_related_score' => ($sameCategory ? 100 : 0) + ($sharedKeywords * 10),
                ];
            })
            ->filter(static fn (array $tool): bool => $tool['_related_score'] > 0)
            ->sortBy([
                ['_related_score', 'desc'],
                ['position', 'asc'],
            ])
            ->take($limit)
            ->map(static function (array $tool): array {
                unset($tool['_related_score']);

                return $tool;
            })
            ->values();
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
                $tool['category_name'],
                implode(' ', $tool['keywords']),
            ])));

            return str_contains($haystack, $needle);
        })->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function categories(bool $includeAll = true): Collection
    {
        $tools = $this->all();
        $counts = $tools->countBy('category');

        $categories = collect(config('tools.categories', []))
            ->map(static fn (array $category, string $slug): array => [
                'slug' => $slug,
                'name' => $category['name'],
                'icon' => $category['icon'],
                'count' => (int) $counts->get($slug, 0),
                'url' => route('tools.category', ['category' => $slug]),
            ])
            ->filter(static fn (array $category): bool => $category['count'] > 0)
            ->values();

        if (! $includeAll) {
            return $categories;
        }

        return $categories->prepend([
            'slug' => 'todas',
            'name' => 'Todos',
            'icon' => 'bi-grid',
            'count' => $tools->count(),
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
        $category = config("tools.categories.{$manifest->category->value}", []);

        return array_merge($manifest->toArray(), [
            'is_active' => $manifest->status->acceptsNewExecutions(),
            'category_name' => (string) ($category['name'] ?? Str::headline($manifest->category->value)),
            'category_icon' => (string) ($category['icon'] ?? 'bi-grid'),
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
