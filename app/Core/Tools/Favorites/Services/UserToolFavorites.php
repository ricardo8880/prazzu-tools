<?php

declare(strict_types=1);

namespace App\Core\Tools\Favorites\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Favorites\Models\UserToolFavorite;
use App\Core\Tools\ToolRegistry;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class UserToolFavorites
{
    public function __construct(
        private ToolRegistry $registry,
        private Router $router,
        private AuditLogger $audit,
    ) {}

    public function isFavorite(string $toolSlug, int $userId): bool
    {
        return UserToolFavorite::query()
            ->where('user_id', $userId)
            ->where('tool_slug', $toolSlug)
            ->exists();
    }

    public function favorite(string $toolSlug, int $userId): bool
    {
        $manifest = $this->requireVisibleTool($toolSlug);

        return DB::transaction(function () use ($manifest, $userId): bool {
            $favorite = UserToolFavorite::query()->firstOrCreate([
                'user_id' => $userId,
                'tool_slug' => $manifest->slug,
            ]);

            if (! $favorite->wasRecentlyCreated) {
                return false;
            }

            $this->audit->record(
                action: 'tool.favorited',
                auditableType: UserToolFavorite::class,
                auditableId: (string) $favorite->getKey(),
                metadata: ['tool_slug' => $manifest->slug],
                actorId: $userId,
            );

            return true;
        });
    }

    public function toggle(string $toolSlug, int $userId): bool
    {
        $manifest = $this->requireVisibleTool($toolSlug);

        return DB::transaction(function () use ($manifest, $userId): bool {
            $favorite = UserToolFavorite::query()
                ->where('user_id', $userId)
                ->where('tool_slug', $manifest->slug)
                ->first();

            if ($favorite instanceof UserToolFavorite) {
                $favoriteId = (string) $favorite->getKey();
                $favorite->delete();

                $this->audit->record(
                    action: 'tool.unfavorited',
                    auditableType: UserToolFavorite::class,
                    auditableId: $favoriteId,
                    metadata: ['tool_slug' => $manifest->slug],
                    actorId: $userId,
                );

                return false;
            }

            $favorite = UserToolFavorite::query()->create([
                'user_id' => $userId,
                'tool_slug' => $manifest->slug,
            ]);

            $this->audit->record(
                action: 'tool.favorited',
                auditableType: UserToolFavorite::class,
                auditableId: (string) $favorite->getKey(),
                metadata: ['tool_slug' => $manifest->slug],
                actorId: $userId,
            );

            return true;
        });
    }

    /** @return Collection<int, array<string, mixed>> */
    public function forUser(int $userId): Collection
    {
        return UserToolFavorite::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->get(['id', 'tool_slug', 'created_at'])
            ->map(function (UserToolFavorite $favorite): ?array {
                $manifest = $this->registry->findManifest((string) $favorite->tool_slug);

                if (! $manifest instanceof ToolManifest || ! $manifest->status->isVisibleInCatalog() || ! $this->router->has($manifest->routeName)) {
                    return null;
                }

                return [
                    'slug' => $manifest->slug,
                    'name' => $manifest->name,
                    'description' => $manifest->description,
                    'icon' => $manifest->icon,
                    'vertical' => $manifest->vertical,
                    'url' => route($manifest->routeName),
                    'favorited_at' => $favorite->created_at,
                ];
            })
            ->filter()
            ->values();
    }

    private function requireVisibleTool(string $toolSlug): ToolManifest
    {
        $manifest = $this->registry->findManifest($toolSlug);

        if (! $manifest instanceof ToolManifest || ! $manifest->status->isVisibleInCatalog() || ! $this->router->has($manifest->routeName)) {
            throw new NotFoundHttpException('Ferramenta não encontrada.');
        }

        return $manifest;
    }
}
