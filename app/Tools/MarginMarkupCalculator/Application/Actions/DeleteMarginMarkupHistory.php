<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupHistoryRepository;

final readonly class DeleteMarginMarkupHistory
{
    public function __construct(
        private RequireOwnedMarginMarkupRun $ownedRun,
        private EloquentMarginMarkupHistoryRepository $history,
        private AuditLogger $audit,
    ) {}

    public function execute(ToolRun $run, int $userId): void
    {
        $run = $this->ownedRun->execute($run, $userId);

        $this->audit->record(
            'tool_run.deleted',
            ToolRun::class,
            $run->id,
            ['tool_slug' => $run->tool_slug],
            $userId,
        );

        $this->history->delete($run);
    }
}
