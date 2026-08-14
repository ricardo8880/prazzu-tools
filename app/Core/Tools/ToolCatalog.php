<?php

namespace App\Core\Tools;

use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Verticals\Application\VerticalContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ToolCatalog
{
    public function __construct(
        private readonly ToolRegistry $registry,
        private readonly ?VerticalContext $verticalContext = null,
    ) {}

    /** @return Collection<int, array<string, mixed>> */
    public function all(bool $onlyCatalogVisible = true): Collection
    {
        return collect($this->registry->manifests($onlyCatalogVisible))
            ->filter(fn (ToolManifest $tool): bool => $this->belongsToActiveVertical($tool))
            ->map(fn (ToolManifest $tool): array => $this->present($tool))
            ->sortBy('position')
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function forVertical(?string $vertical, bool $onlyCatalogVisible = true): Collection
    {
        $vertical = $vertical === null ? null : trim($vertical);

        return collect($this->registry->manifests($onlyCatalogVisible))
            ->filter(static fn (ToolManifest $tool): bool => $vertical === null || $tool->vertical === $vertical)
            ->map(fn (ToolManifest $tool): array => $this->present($tool))
            ->sortBy('position')
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function byCategory(string $category): Collection
    {
        $field = $category === 'documentos' ? 'declared_category' : 'category';

        return $this->all()->where($field, $category)->values();
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
            ->sortByDesc('release_order')
            ->take(max(0, $limit))
            ->values();
    }

    /** @return array<string, mixed>|null */
    public function find(string $slug): ?array
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function nextSteps(string $slug, int $limit = 4): Collection
    {
        $current = $this->find($slug);

        if ($current === null || $limit < 1) {
            return collect();
        }

        $available = $this->all()
            ->reject(static fn (array $tool): bool => $tool['slug'] === $slug)
            ->keyBy('slug');

        return collect(config("tools.journeys.{$slug}", []))
            ->filter(static fn (mixed $candidate): bool => is_string($candidate) && trim($candidate) !== '')
            ->map(static fn (string $candidate): string => trim($candidate))
            ->unique()
            ->map(static fn (string $candidate): ?array => $available->get($candidate))
            ->filter()
            ->take($limit)
            ->values()
            ->map(static fn (array $tool, int $index): array => [
                ...$tool,
                'journey_position' => $index + 1,
                'is_primary_next_step' => $index === 0,
            ]);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function related(string $slug, int $limit = 4): Collection
    {
        $current = $this->find($slug);

        if ($current === null || $limit < 1) {
            return collect();
        }

        $available = $this->all()
            ->reject(static fn (array $tool): bool => $tool['slug'] === $slug)
            ->keyBy('slug');

        $curatedSlugs = collect(config("tools.journeys.{$slug}", []))
            ->filter(static fn (mixed $candidate): bool => is_string($candidate) && trim($candidate) !== '')
            ->map(static fn (string $candidate): string => trim($candidate))
            ->unique()
            ->values();

        $curated = $curatedSlugs
            ->map(static fn (string $candidate): ?array => $available->get($candidate))
            ->filter()
            ->take($limit)
            ->values();

        if ($curated->count() >= $limit) {
            return $curated;
        }

        $keywords = collect($current['keywords'])
            ->map(static fn (string $keyword): string => Str::lower(Str::ascii($keyword)))
            ->all();
        $excludedSlugs = $curated->pluck('slug')->push($slug)->all();

        $fallback = $available
            ->reject(static fn (array $tool): bool => in_array($tool['slug'], $excludedSlugs, true))
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
            ->take($limit - $curated->count())
            ->map(static function (array $tool): array {
                unset($tool['_related_score']);

                return $tool;
            })
            ->values();

        return $curated->concat($fallback)->values();
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
                $tool['declared_category'],
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
        $declaredCategory = $manifest->category->value;
        $publicCategory = $declaredCategory === 'documentos' ? 'geradores' : $declaredCategory;
        $category = config("tools.categories.{$publicCategory}", []);

        return array_merge($manifest->toArray(), [
            'release_order' => $this->releaseOrder($manifest->slug),
            'is_active' => $manifest->status->acceptsNewExecutions(),
            'declared_category' => $declaredCategory,
            'category' => $publicCategory,
            'category_name' => (string) ($category['name'] ?? Str::headline($publicCategory)),
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

    private function belongsToActiveVertical(ToolManifest $manifest): bool
    {
        $activeSlug = $this->verticalContext?->active()?->slug;

        if ($activeSlug === null) {
            $configuredDefault = config('verticals.default');
            $activeSlug = is_string($configuredDefault) && trim($configuredDefault) !== ''
                ? trim($configuredDefault)
                : null;
        }

        return $activeSlug === null || $manifest->vertical === $activeSlug;
    }

    private function releaseOrder(string $slug): int
    {
        $entry = collect(config('product_tools.official', []))->firstWhere('slug', $slug);

        return (int) ($entry['release_order'] ?? 0);
    }
}
