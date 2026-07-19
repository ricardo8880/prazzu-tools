<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Dates\ReferenceDate;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Throwable;

final readonly class DatabaseToolRunHistory implements ToolRunHistory
{
    public function __construct(
        private ToolRunRecorder $recorder,
        private AuditLogger $audit,
    ) {}

    public function recordSucceeded(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        array $result,
        int $userId,
    ): ToolRunEntry {
        $run = $this->recorder->start(
            $module,
            $ruleVersion,
            $referenceDate,
            $input,
            $userId,
        );

        try {
            $run = $this->recorder->succeed($run, $result);
        } catch (Throwable $exception) {
            try {
                $this->recorder->fail($run, 'history.record_failed');
            } catch (Throwable) {
                // Preserve the original recording failure.
            }

            throw $exception;
        }

        return $this->toEntry($run, false);
    }

    public function recentSucceeded(string $toolSlug, int $userId, int $limit = 24): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException('O limite do histórico deve estar entre 1 e 100.');
        }

        return $this->ownedSucceededRuns($toolSlug, $userId)
            ->withExists([
                'favorites as is_favorite' => static fn (Builder $query): Builder => $query->where('user_id', $userId),
            ])
            ->latest('reference_date')
            ->latest('finished_at')
            ->limit($limit)
            ->get()
            ->map(fn (ToolRun $run): ToolRunEntry => $this->toEntry($run, (bool) $run->getAttribute('is_favorite')))
            ->values()
            ->all();
    }

    public function paginateSucceeded(ToolRunHistoryQuery $query): ToolRunPage
    {
        $runs = $this->ownedSucceededRuns($query->toolSlug, $query->userId)
            ->withExists([
                'favorites as is_favorite' => static fn (Builder $favoriteQuery): Builder => $favoriteQuery->where('user_id', $query->userId),
            ])
            ->when($query->from !== null, static fn (Builder $builder): Builder => $builder->whereDate('reference_date', '>=', $query->from?->format('Y-m-d')))
            ->when($query->to !== null, static fn (Builder $builder): Builder => $builder->whereDate('reference_date', '<=', $query->to?->format('Y-m-d')))
            ->when($query->favoritesOnly, static fn (Builder $builder): Builder => $builder->whereHas(
                'favorites',
                static fn (Builder $favoriteQuery): Builder => $favoriteQuery->where('user_id', $query->userId),
            ))
            ->latest('reference_date')
            ->latest('finished_at')
            ->paginate(
                perPage: $query->perPage,
                page: $query->page,
            );

        return new ToolRunPage(
            items: $runs->getCollection()
                ->map(fn (ToolRun $run): ToolRunEntry => $this->toEntry($run, (bool) $run->getAttribute('is_favorite')))
                ->values()
                ->all(),
            page: $runs->currentPage(),
            perPage: $runs->perPage(),
            total: $runs->total(),
            lastPage: $runs->lastPage(),
        );
    }

    public function findSucceededOwned(string $toolSlug, string $runId, int $userId): ToolRunEntry
    {
        $run = $this->ownedSucceededRuns($toolSlug, $userId)
            ->withExists([
                'favorites as is_favorite' => static fn (Builder $query): Builder => $query->where('user_id', $userId),
            ])
            ->whereKey($runId)
            ->firstOrFail();

        return $this->toEntry($run, (bool) $run->getAttribute('is_favorite'));
    }

    public function deleteSucceededOwned(string $toolSlug, string $runId, int $userId): void
    {
        $run = $this->ownedSucceededRuns($toolSlug, $userId)
            ->whereKey($runId)
            ->firstOrFail();

        $this->audit->record(
            action: 'tool_run.deleted',
            auditableType: ToolRun::class,
            auditableId: (string) $run->id,
            metadata: ['tool_slug' => $toolSlug],
            actorId: $userId,
        );

        $run->delete();
    }

    /** @return Builder<ToolRun> */
    private function ownedSucceededRuns(string $toolSlug, int $userId): Builder
    {
        return ToolRun::query()
            ->where('tool_slug', $toolSlug)
            ->where('user_id', $userId)
            ->where('status', ToolRunStatus::Succeeded);
    }

    private function toEntry(ToolRun $run, bool $favorite): ToolRunEntry
    {
        return new ToolRunEntry(
            id: (string) $run->id,
            toolSlug: (string) $run->tool_slug,
            referenceDate: DateTimeImmutable::createFromInterface($run->reference_date),
            input: is_array($run->input_payload) ? $run->input_payload : [],
            result: is_array($run->result_payload) ? $run->result_payload : [],
            createdAt: DateTimeImmutable::createFromInterface($run->created_at),
            finishedAt: DateTimeImmutable::createFromInterface($run->finished_at ?? $run->created_at),
            toolVersion: (string) $run->tool_version,
            ruleVersion: (string) $run->rule_version,
            favorite: $favorite,
        );
    }
}
