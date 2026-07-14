<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;

final readonly class DeleteValidationHistory
{
    private const TOOL_SLUG = 'validador-de-cnpj';

    public function __construct(private AuditLogger $audit)
    {
    }

    public function execute(ToolRun $run, int $userId): void
    {
        abort_unless(
            $run->user_id === $userId
            && $run->tool_slug === self::TOOL_SLUG
            && $run->status === ToolRunStatus::Succeeded,
            404,
        );

        $this->audit->record(
            action: 'tool_run.deleted',
            auditableType: ToolRun::class,
            auditableId: $run->id,
            metadata: ['tool_slug' => $run->tool_slug],
            actorId: $userId,
        );

        $run->delete();
    }
}
