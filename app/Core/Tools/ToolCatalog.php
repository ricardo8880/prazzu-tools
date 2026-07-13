<?php

namespace App\Core\Tools;

use App\Core\Tools\Data\ToolDefinition;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ToolCatalog
{
    public function __construct(private readonly ToolRegistry $registry)
    {
    }

    /** @return Collection<int, array<string, mixed>> */
    public function all(bool $onlyActive = true): Collection
    {
        $configured = collect(config('tools.catalog', []))
            ->keyBy('slug');

        $catalog = $configured;

        foreach ($this->registry->definitions($onlyActive) as $definition) {
            $catalog->put(
                $definition->slug,
                array_replace(
                    $configured->get($definition->slug, []),
                    $this->fromDefinition($definition),
                ),
            );
        }

        return $catalog
            ->filter(fn (array $tool): bool => ! $onlyActive || ($tool['is_active'] ?? true))
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function featured(): Collection
    {
        return $this->all()
            ->filter(fn (array $tool): bool => (bool) ($tool['is_featured'] ?? false))
            ->sortBy(fn (array $tool): int => (int) ($tool['position'] ?? PHP_INT_MAX))
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function popular(int $limit = 5): Collection
    {
        return $this->all()
            ->filter(fn (array $tool): bool => (bool) ($tool['is_popular'] ?? false))
            ->sortByDesc(fn (array $tool): int => (int) ($tool['uses_count'] ?? 0))
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
                implode(' ', $tool['keywords'] ?? []),
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

        $tools = $this->all()->reject(fn (array $tool): bool => $tool['slug'] === $slug);

        return $tools
            ->sortByDesc(fn (array $tool): int => $tool['category'] === $current['category'] ? 1 : 0)
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function categories(bool $includeAll = true): Collection
    {
        $counts = $this->all()->countBy('category');

        $categories = collect(config('tools.categories', []))
            ->map(function (array $category, string $slug) use ($counts): array {
                return [
                    'slug' => $slug,
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'count' => (int) $counts->get($slug, 0),
                    'url' => route('tools.category', ['category' => $slug]),
                ];
            })
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
    private function fromDefinition(ToolDefinition $definition): array
    {
        return [
            'slug' => $definition->slug,
            'name' => $definition->name,
            'description' => $definition->description,
            'category' => $definition->category,
            'icon' => $definition->icon,
            'route_name' => $definition->routeName,
            'is_free' => $definition->isFree,
            'is_premium' => $definition->isPremium,
            'is_featured' => $definition->isFeatured,
            'is_active' => $definition->isActive,
            'keywords' => $definition->keywords,
            'tone' => 'purple',
            'badge' => $definition->isPremium ? 'Premium' : 'Grátis',
            'badge_tone' => $definition->isPremium ? 'yellow' : 'green',
            'is_popular' => false,
            'uses_count' => 0,
            'uses_label' => null,
            'position' => PHP_INT_MAX,
        ];
    }
}
