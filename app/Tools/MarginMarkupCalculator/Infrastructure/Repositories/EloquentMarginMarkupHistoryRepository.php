<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Infrastructure\Repositories;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentMarginMarkupHistoryRepository
{
    private const TOOL_SLUG = 'calculadora-margem-markup';

    /** @return LengthAwarePaginator<int, ToolRun> */
    public function paginateSucceededForUser(
        int $userId,
        ?string $from,
        ?string $to,
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = ToolRun::query()
            ->where('user_id', $userId)
            ->where('tool_slug', self::TOOL_SLUG)
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('finished_at');

        if ($from !== null && $from !== '') {
            $query->whereDate('reference_date', '>=', $from);
        }

        if ($to !== null && $to !== '') {
            $query->whereDate('reference_date', '<=', $to);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findOrFail(string $id): ToolRun
    {
        return ToolRun::query()->findOrFail($id);
    }

    public function delete(ToolRun $run): void
    {
        $run->delete();
    }
}
