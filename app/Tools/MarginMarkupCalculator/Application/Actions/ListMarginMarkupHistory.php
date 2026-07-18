<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupHistoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListMarginMarkupHistory
{
    public function __construct(
        private EloquentMarginMarkupHistoryRepository $history,
    ) {}

    /** @return LengthAwarePaginator<int, ToolRun> */
    public function execute(int $userId, ?string $from, ?string $to): LengthAwarePaginator
    {
        return $this->history->paginateSucceededForUser($userId, $from, $to);
    }
}
