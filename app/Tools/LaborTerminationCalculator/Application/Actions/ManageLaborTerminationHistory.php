<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Application\Actions;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Tools\History\Models\ToolRun;
use App\Tools\LaborTerminationCalculator\Infrastructure\Repositories\LaborTerminationHistoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class ManageLaborTerminationHistory
{
    public function __construct(
        private LaborTerminationHistoryRepository $history,
        private AuditLogger $audit,
    ) {}

    /** @return Collection<int, ToolRun> */
    public function recent(int $userId, int $limit = 3): Collection
    {
        return $this->history->recentForUser($userId, $limit);
    }

    /** @return LengthAwarePaginator<int, ToolRun> */
    public function paginate(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->history->paginateForUser($userId, $perPage);
    }

    public function owned(ToolRun $run, int $userId): ToolRun
    {
        return $this->history->ownedByUser($run, $userId);
    }

    public function delete(ToolRun $run, int $userId): void
    {
        $run = $this->owned($run, $userId);

        $this->audit->record(
            action: 'tool_run.deleted',
            auditableType: ToolRun::class,
            auditableId: $run->id,
            metadata: ['tool_slug' => $run->tool_slug],
            actorId: $userId,
        );

        $this->history->delete($run);
    }
}
