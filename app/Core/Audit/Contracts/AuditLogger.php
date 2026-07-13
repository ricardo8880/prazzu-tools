<?php

namespace App\Core\Audit\Contracts;

use App\Core\Audit\Models\AuditLog;

interface AuditLogger
{
    /** @param array<string, mixed> $metadata */
    public function record(
        string $action,
        ?string $auditableType = null,
        ?string $auditableId = null,
        array $metadata = [],
        ?int $actorId = null,
    ): AuditLog;
}
