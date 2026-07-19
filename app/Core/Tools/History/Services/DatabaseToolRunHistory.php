<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Dates\ReferenceDate;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

final readonly class DatabaseToolRunHistory implements ToolRunHistory
{
    public function __construct(
        private ToolRunRecorder $recorder,
        private AuditLogger $audit,
    ) {}

    public function recordSucceeded(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        array $result,
        int $userId,
    ): ToolRunEntry {
        $run = $this->recorder->start(
            $module,
            $ruleVersion,
            $referenceDate,
            $input,
            $userId,
        );

        try {
            $run = $this->recorder->succeed($run, $result);
        } catch (Throwable $exception) {
            try {
                $this->recorder->fail($run, 'history.record_failed');
            } catch (Throwable) {
                // Preserve the original recording failure.
            }

            throw $exception;
        }

        return $this->toEntry($run);
    }

    public function recentSucceeded(string $toolSlug, int $userId, int $limit = 24): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException('O limite do histórico deve estar entre 1 e 100.');
        }

        return ToolRun::query()
            ->where('tool_slug', $toolSlug)
            ->where('user_id', $userId)
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('reference_date')
            ->latest('finished_at')
            ->limit($limit)
            ->get()
            ->map(fn (ToolRun $run): ToolRunEntry => $this->toEntry($run))
            ->values()
            ->all();
    }

    public function deleteSucceededOwned(string $toolSlug, string $runId, int $userId): void
    {
        $run = ToolRun::query()
            ->whereKey($runId)
            ->where('tool_slug', $toolSlug)
            ->where('user_id', $userId)
            ->where('status', ToolRunStatus::Succeeded)
            ->firstOrFail();

        $this->audit->record(
            action: 'tool_run.deleted',
            auditableType: ToolRun::class,
            auditableId: $run->id,
            metadata: ['tool_slug' => $toolSlug],
            actorId: $userId,
        );

        $run->delete();
    }

    private function toEntry(ToolRun $run): ToolRunEntry
    {
        return new ToolRunEntry(
            id: (string) $run->id,
            toolSlug: (string) $run->tool_slug,
            referenceDate: DateTimeImmutable::createFromInterface($run->reference_date),
            input: is_array($run->input_payload) ? $run->input_payload : [],
            result: is_array($run->result_payload) ? $run->result_payload : [],
            createdAt: DateTimeImmutable::createFromInterface($run->created_at),
        );
    }
}
