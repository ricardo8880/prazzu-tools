<?php

namespace App\Core\Tools;

use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ToolCatalog
{
    public function __construct(private readonly ToolRegistry $registry)
    {
    }

    /** @return Collection<int, array<string, mixed>> */
    public function all(bool $onlyCatalogVisible = true): Collection
    {
        $manifests = collect(config('tools.catalog', []))
            ->map(static fn (array $tool): ToolManifest => ToolManifest::fromArray($tool))
            ->keyBy(static fn (ToolManifest $tool): string => $tool->slug);

        foreach ($this->registry->manifests($onlyCatalogVisible) as $manifest) {
            $manifests->put($manifest->slug, $manifest);
        }

        $metrics = config('tools.metrics', []);

        return $manifests
            ->when(
                $onlyCatalogVisible,
                fn (Collection $items): Collection => $items->filter(
                    static fn (ToolManifest $tool): bool => $tool->status->isVisibleInCatalog(),
                ),
            )
            ->map(fn (ToolManifest $tool): array => $this->present(
                $tool,
                is_array($metrics[$tool->slug] ?? null) ? $metrics[$tool->slug] : [],
            ))
            ->sortBy('position')
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function featured(): Collection
    {
        return $this->all()
            ->filter(static fn (array $tool): bool => (bool) $tool['is_featured'])
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function popular(int $limit = 5): Collection
    {
        return $this->all()
            ->filter(static fn (array $tool): bool => (bool) $tool['is_popular'])
            ->sortByDesc(static fn (array $tool): int => (int) $tool['uses_count'])
            ->take($limit)
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
            $tools = $tools->where('category', $category);
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
    public function related(string $slug, int $limit = 3): Collection
    {
        $current = $this->find($slug);

        if ($current === null) {
            return collect();
        }

        return $this->all()
            ->reject(static fn (array $tool): bool => $tool['slug'] === $slug)
            ->sortByDesc(static fn (array $tool): int => $tool['category'] === $current['category'] ? 1 : 0)
            ->take($limit)
            ->values();
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

    /** @param array<string, mixed> $metrics @return array<string, mixed> */
    private function present(ToolManifest $manifest, array $metrics): array
    {
        $isPremium = $manifest->access === ToolAccess::Premium;

        return array_merge($manifest->toArray(), [
            'is_free' => $manifest->access === ToolAccess::Free,
            'is_premium' => $isPremium,
            'is_active' => $manifest->status->acceptsNewExecutions(),
            'tone' => (string) ($metrics['tone'] ?? 'purple'),
            'badge' => (string) ($metrics['badge'] ?? ($isPremium ? 'Premium' : 'Grátis')),
            'badge_tone' => (string) ($metrics['badge_tone'] ?? ($isPremium ? 'yellow' : 'green')),
            'is_popular' => (bool) ($metrics['is_popular'] ?? false),
            'uses_count' => (int) ($metrics['uses_count'] ?? 0),
            'uses_label' => $metrics['uses_label'] ?? null,
        ]);
    }
}
