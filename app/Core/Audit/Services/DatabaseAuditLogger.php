<?php

namespace App\Core\Audit\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Audit\Models\AuditLog;
use InvalidArgumentException;

final class DatabaseAuditLogger implements AuditLogger
{
    public function record(
        string $action,
        ?string $auditableType = null,
        ?string $auditableId = null,
        array $metadata = [],
        ?int $actorId = null,
    ): AuditLog {
        if (! preg_match('/^[a-z0-9_.-]{3,120}$/', $action)) {
            throw new InvalidArgumentException('A ação de auditoria possui formato inválido.');
        }

        if (($auditableType === null) !== ($auditableId === null)) {
            throw new InvalidArgumentException('Tipo e identificador auditável devem ser informados em conjunto.');
        }

        return AuditLog::query()->create([
            'actor_id' => $actorId,
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}
