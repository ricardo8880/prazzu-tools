<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ListValidationHistory
{
    private const TOOL_SLUG = 'validador-de-cnpj';

    /** @return Collection<int, ToolRun> */
    public function recent(int $userId, int $limit = 3): Collection
    {
        return $this->query($userId)
            ->limit($limit)
            ->get();
    }

    /** @return LengthAwarePaginator<ToolRun> */
    public function paginate(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->query($userId)->paginate($perPage);
    }

    private function query(int $userId)
    {
        return ToolRun::query()
            ->where('user_id', $userId)
            ->where('tool_slug', self::TOOL_SLUG)
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('finished_at');
    }
}
