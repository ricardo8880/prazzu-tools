<?php

declare(strict_types=1);

namespace App\Core\Tools\Discovery\Application;

use App\Core\Tools\ToolCatalog;
use App\Core\Verticals\Application\VerticalContext;
use Illuminate\Support\Collection;

final readonly class ProblemJourneyCatalog
{
    public function __construct(
        private ToolCatalog $tools,
        private VerticalContext $verticalContext,
    ) {}

    /** @return Collection<int, array<string, mixed>> */
    public function forActiveVertical(int $stepLimit = 4): Collection
    {
        $vertical = $this->verticalContext->slug();

        if ($vertical === null || $stepLimit < 1) {
            return collect();
        }

        return collect(config("tools.discovery_journeys.{$vertical}", []))
            ->filter(static fn (mixed $journey): bool => is_array($journey))
            ->map(fn (array $journey, int $index): ?array => $this->present($journey, $index + 1, $stepLimit))
            ->filter()
            ->values();
    }

    /** @return array<string, mixed>|null */
    public function findForActiveVertical(string $key, int $stepLimit = 4): ?array
    {
        $key = trim($key);

        if ($key === '') {
            return null;
        }

        return $this->forActiveVertical($stepLimit)->firstWhere('key', $key);
    }

    /** @return array<string, mixed>|null */
    private function present(array $journey, int $position, int $stepLimit): ?array
    {
        $key = trim((string) ($journey['key'] ?? ''));
        $startSlug = trim((string) ($journey['start_slug'] ?? ''));
        $start = $startSlug === '' ? null : $this->tools->find($startSlug);

        if ($key === '' || $start === null) {
            return null;
        }

        $steps = collect([$start])
            ->concat($this->tools->nextSteps($startSlug, max(0, $stepLimit - 1)))
            ->unique('slug')
            ->take($stepLimit)
            ->values()
            ->map(static fn (array $tool, int $stepIndex): array => [
                'slug' => $tool['slug'],
                'name' => $tool['name'],
                'icon' => $tool['icon'],
                'route_name' => $tool['route_name'],
                'position' => $stepIndex + 1,
            ]);

        if ($steps->count() < 2) {
            return null;
        }

        return [
            'key' => $key,
            'title' => trim((string) ($journey['title'] ?? $start['name'])),
            'description' => trim((string) ($journey['description'] ?? '')),
            'icon' => trim((string) ($journey['icon'] ?? $start['icon'])),
            'position' => $position,
            'start_slug' => $startSlug,
            'start_name' => $start['name'],
            'start_route_name' => $start['route_name'],
            'steps' => $steps,
        ];
    }
}
