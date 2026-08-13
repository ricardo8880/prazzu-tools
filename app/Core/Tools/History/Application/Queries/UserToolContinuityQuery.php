<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Application\Queries;

use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Tools\ToolRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

final readonly class UserToolContinuityQuery
{
    public function __construct(
        private ToolRegistry $registry,
        private Router $router,
    ) {}

    /**
     * @return array{
     *     historyCount:int,
     *     favoriteCount:int,
     *     usedToolCount:int,
     *     continueRuns:Collection<int, array<string, mixed>>,
     *     favoriteRuns:Collection<int, array<string, mixed>>,
     *     historyTools:Collection<int, array<string, mixed>>
     * }
     */
    public function summary(int $userId): array
    {
        $historyCount = $this->ownedSucceededRuns($userId)->count();
        $favoriteCount = $this->ownedSucceededRuns($userId)
            ->whereHas('favorites', static fn ($query) => $query->where('user_id', $userId))
            ->count();
        $usedToolCount = $this->ownedSucceededRuns($userId)->distinct()->count('tool_slug');

        $recentRuns = $this->ownedSucceededRuns($userId)
            ->select(['id', 'tool_slug', 'reference_date', 'finished_at', 'created_at'])
            ->withExists([
                'favorites as is_favorite' => static fn ($query) => $query->where('user_id', $userId),
            ])
            ->latest('finished_at')
            ->latest('created_at')
            ->limit(24)
            ->get();

        $continueRuns = $recentRuns
            ->map(fn (ToolRun $run): ?array => $this->presentRun($run))
            ->filter()
            ->unique('tool_slug')
            ->take(4)
            ->values();

        $favoriteRuns = $this->ownedSucceededRuns($userId)
            ->select(['id', 'tool_slug', 'reference_date', 'finished_at', 'created_at'])
            ->whereHas('favorites', static fn ($query) => $query->where('user_id', $userId))
            ->latest('finished_at')
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(fn (ToolRun $run): ?array => $this->presentRun($run, true))
            ->filter()
            ->values();

        $historyTools = $this->ownedSucceededRuns($userId)
            ->select('tool_slug')
            ->selectRaw('COUNT(*) as runs_count')
            ->selectRaw('MAX(finished_at) as last_used_at')
            ->groupBy('tool_slug')
            ->orderByDesc('last_used_at')
            ->limit(8)
            ->get()
            ->map(fn (ToolRun $run): ?array => $this->presentHistoryTool($run))
            ->filter()
            ->values();

        return compact(
            'historyCount',
            'favoriteCount',
            'usedToolCount',
            'continueRuns',
            'favoriteRuns',
            'historyTools',
        );
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentTools(int $userId, ?string $verticalSlug, int $limit = 4): Collection
    {
        if ($limit < 1) {
            return collect();
        }

        return $this->ownedSucceededRuns($userId)
            ->select(['id', 'tool_slug', 'reference_date', 'finished_at', 'created_at'])
            ->latest('finished_at')
            ->latest('created_at')
            ->limit(max(24, $limit * 8))
            ->get()
            ->map(fn (ToolRun $run): ?array => $this->presentRun($run))
            ->filter()
            ->when(
                is_string($verticalSlug) && trim($verticalSlug) !== '',
                static fn (Collection $runs): Collection => $runs->where('tool_vertical', trim($verticalSlug)),
            )
            ->unique('tool_slug')
            ->take($limit)
            ->values();
    }

    /** @return Builder<ToolRun> */
    private function ownedSucceededRuns(int $userId): Builder
    {
        return ToolRun::query()
            ->where('user_id', $userId)
            ->where('status', ToolRunStatus::Succeeded);
    }

    /** @return array<string, mixed>|null */
    private function presentRun(ToolRun $run, bool $favorite = false): ?array
    {
        $manifest = $this->registry->findManifest((string) $run->tool_slug);

        if (! $manifest instanceof ToolManifest || ! $this->router->has($manifest->routeName)) {
            return null;
        }

        $routePrefix = $this->routePrefix($manifest->routeName);
        $historyRoute = $routePrefix === null ? null : $routePrefix.'.history.index';
        $showRoute = $routePrefix === null ? null : $routePrefix.'.history.show';
        $repeatRoute = $routePrefix === null ? null : $routePrefix.'.history.repeat';

        $historyUrl = $historyRoute !== null && $this->router->has($historyRoute)
            ? route($historyRoute)
            : null;
        $showUrl = $showRoute !== null && $this->router->has($showRoute)
            ? route($showRoute, [$run->id])
            : null;
        $repeatUrl = $repeatRoute !== null && $this->router->has($repeatRoute)
            ? route($repeatRoute, [$run->id])
            : null;

        return [
            'id' => (string) $run->id,
            'tool_slug' => $manifest->slug,
            'tool_name' => $manifest->name,
            'tool_description' => $manifest->description,
            'tool_icon' => $manifest->icon,
            'tool_vertical' => $manifest->vertical,
            'tool_url' => route($manifest->routeName),
            'history_url' => $historyUrl,
            'open_url' => $showUrl ?? $historyUrl ?? route($manifest->routeName),
            'repeat_url' => $repeatUrl,
            'reference_date' => $run->reference_date,
            'finished_at' => $run->finished_at ?? $run->created_at,
            'favorite' => $favorite || (bool) $run->getAttribute('is_favorite'),
        ];
    }

    /** @return array<string, mixed>|null */
    private function presentHistoryTool(ToolRun $summary): ?array
    {
        $manifest = $this->registry->findManifest((string) $summary->tool_slug);

        if (! $manifest instanceof ToolManifest || ! $this->router->has($manifest->routeName)) {
            return null;
        }

        $routePrefix = $this->routePrefix($manifest->routeName);
        $historyRoute = $routePrefix === null ? null : $routePrefix.'.history.index';

        return [
            'tool_slug' => $manifest->slug,
            'tool_name' => $manifest->name,
            'tool_icon' => $manifest->icon,
            'tool_vertical' => $manifest->vertical,
            'tool_url' => route($manifest->routeName),
            'history_url' => $historyRoute !== null && $this->router->has($historyRoute)
                ? route($historyRoute)
                : null,
            'runs_count' => (int) $summary->getAttribute('runs_count'),
            'last_used_at' => $summary->getAttribute('last_used_at'),
        ];
    }

    private function routePrefix(string $routeName): ?string
    {
        if (! str_ends_with($routeName, '.index')) {
            return null;
        }

        return substr($routeName, 0, -strlen('.index'));
    }
}
