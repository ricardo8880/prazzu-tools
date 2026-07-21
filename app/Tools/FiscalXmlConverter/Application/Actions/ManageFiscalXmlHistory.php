<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Tools\FiscalXmlConverter\Tool;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ManageFiscalXmlHistory
{
    public function __construct(private ToolRunHistory $history) {}

    /** @return list<ToolRunEntry> */
    public function recent(int $userId, int $limit = 3): array
    {
        return $this->history->recentSucceeded(Tool::SLUG, $userId, $limit);
    }

    /** @return LengthAwarePaginator<int, ToolRunEntry> */
    public function paginate(int $userId, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $result = $this->history->paginateSucceeded(new ToolRunHistoryQuery(Tool::SLUG, $userId, $page, $perPage));
        return new LengthAwarePaginator($result->items, $result->total, $result->perPage, $result->page, [
            'path' => request()->url(), 'query' => request()->query(),
        ]);
    }

    public function owned(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(Tool::SLUG, $runId, $userId);
    }

    public function delete(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(Tool::SLUG, $runId, $userId);
    }
}
