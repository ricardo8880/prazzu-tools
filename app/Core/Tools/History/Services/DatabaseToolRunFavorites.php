<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Tools\History\Models\ToolRunFavorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final readonly class DatabaseToolRunFavorites implements ToolRunFavorites
{
    public function __construct(private AuditLogger $audit) {}

    public function favoriteOwned(string $toolSlug, string $runId, int $userId): void
    {
        $run = $this->ownedSucceededRun($toolSlug, $runId, $userId)->firstOrFail();

        ToolRunFavorite::query()->firstOrCreate([
            'tool_run_id' => $run->id,
            'user_id' => $userId,
        ]);

        $this->audit->record(
            action: 'tool_run.favorited',
            auditableType: ToolRun::class,
            auditableId: (string) $run->id,
            metadata: ['tool_slug' => $toolSlug],
            actorId: $userId,
        );
    }

    public function unfavoriteOwned(string $toolSlug, string $runId, int $userId): void
    {
        $run = $this->ownedSucceededRun($toolSlug, $runId, $userId)->firstOrFail();

        ToolRunFavorite::query()
            ->where('tool_run_id', $run->id)
            ->where('user_id', $userId)
            ->delete();

        $this->audit->record(
            action: 'tool_run.unfavorited',
            auditableType: ToolRun::class,
            auditableId: (string) $run->id,
            metadata: ['tool_slug' => $toolSlug],
            actorId: $userId,
        );
    }

    public function toggleOwned(string $toolSlug, string $runId, int $userId): bool
    {
        return DB::transaction(function () use ($toolSlug, $runId, $userId): bool {
            if ($this->isFavoriteOwned($toolSlug, $runId, $userId)) {
                $this->unfavoriteOwned($toolSlug, $runId, $userId);

                return false;
            }

            $this->favoriteOwned($toolSlug, $runId, $userId);

            return true;
        });
    }

    public function isFavoriteOwned(string $toolSlug, string $runId, int $userId): bool
    {
        $run = $this->ownedSucceededRun($toolSlug, $runId, $userId)->firstOrFail();

        return ToolRunFavorite::query()
            ->where('tool_run_id', $run->id)
            ->where('user_id', $userId)
            ->exists();
    }

    /** @return Builder<ToolRun> */
    private function ownedSucceededRun(string $toolSlug, string $runId, int $userId): Builder
    {
        return ToolRun::query()
            ->whereKey($runId)
            ->where('tool_slug', $toolSlug)
            ->where('user_id', $userId)
            ->where('status', ToolRunStatus::Succeeded);
    }
}
