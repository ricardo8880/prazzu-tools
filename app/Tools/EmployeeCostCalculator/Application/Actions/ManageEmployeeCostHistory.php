<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use DateTimeImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ManageEmployeeCostHistory
{
    private const TOOL_SLUG = 'custo-funcionario-clt';

    public function __construct(private ToolRunHistory $history) {}

    /** @return LengthAwarePaginator<int, ToolRunEntry> */
    public function paginate(int $userId, ?string $from, ?string $to, int $page = 1): LengthAwarePaginator
    {
        $result = $this->history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: self::TOOL_SLUG,
            userId: $userId,
            page: $page,
            perPage: 10,
            from: $from ? new DateTimeImmutable($from) : null,
            to: $to ? new DateTimeImmutable($to) : null,
        ));

        return new LengthAwarePaginator(
            $result->items,
            $result->total,
            $result->perPage,
            $result->page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }

    public function find(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }

    /** @return array<string, mixed> */
    public function repeat(string $runId, int $userId): array
    {
        return $this->find($runId, $userId)->input;
    }

    public function delete(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
