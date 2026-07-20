<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use DateTimeImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListTaxComparisonHistory
{
    private const TOOL_SLUG = 'comparador-tributario';

    public function __construct(private ToolRunHistory $history) {}

    /** @return LengthAwarePaginator<int, ToolRunEntry> */
    public function execute(int $userId, ?string $from, ?string $to, int $page = 1): LengthAwarePaginator
    {
        $result = $this->history->paginateSucceeded(new ToolRunHistoryQuery(
            self::TOOL_SLUG,
            $userId,
            $page,
            10,
            $from ? new DateTimeImmutable($from) : null,
            $to ? new DateTimeImmutable($to) : null,
        ));

        return new LengthAwarePaginator(
            $result->items,
            $result->total,
            $result->perPage,
            $result->page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }
}
