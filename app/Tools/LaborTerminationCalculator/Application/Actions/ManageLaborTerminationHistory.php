<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ManageLaborTerminationHistory
{
    private const TOOL_SLUG = 'calculadora-de-rescisao';

    public function __construct(private ToolRunHistory $history) {}

    /** @return list<ToolRunEntry> */
    public function recent(int $userId, int $limit = 3): array
    {
        return $this->history->recentSucceeded(self::TOOL_SLUG, $userId, $limit);
    }

    /** @return LengthAwarePaginator<int, ToolRunEntry> */
    public function paginate(int $userId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        $result = $this->history->paginateSucceeded(new ToolRunHistoryQuery(self::TOOL_SLUG, $userId, $page, $perPage));

        return new LengthAwarePaginator($result->items, $result->total, $result->perPage, $result->page, ['path' => request()->url(), 'query' => request()->query()]);
    }

    public function owned(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }

    public function delete(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
