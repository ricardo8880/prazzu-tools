<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Infrastructure\Repositories;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class LaborTerminationHistoryRepository
{
    /** @return Collection<int, ToolRun> */
    public function recentForUser(int $userId, int $limit): Collection
    {
        return $this->queryForUser($userId)->limit($limit)->get();
    }

    /** @return LengthAwarePaginator<int, ToolRun> */
    public function paginateForUser(int $userId, int $perPage): LengthAwarePaginator
    {
        return $this->queryForUser($userId)->paginate($perPage);
    }

    public function ownedByUser(ToolRun $run, int $userId): ToolRun
    {
        abort_unless(
            $run->user_id === $userId
            && $run->tool_slug === 'calculadora-de-rescisao'
            && $run->status === ToolRunStatus::Succeeded,
            404,
        );

        return $run;
    }

    public function delete(ToolRun $run): void
    {
        $run->delete();
    }

    /** @return Builder<ToolRun> */
    private function queryForUser(int $userId): Builder
    {
        return ToolRun::query()
            ->where('user_id', $userId)
            ->where('tool_slug', 'calculadora-de-rescisao')
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('finished_at');
    }
}
