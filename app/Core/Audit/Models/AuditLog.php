<?php

namespace App\Core\Audit\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use LogicException;

final class AuditLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'action',
        'auditable_type',
        'auditable_id',
        'metadata',
        'occurred_at',
    ];

    protected static function booted(): void
    {
        self::updating(static function (): never {
            throw new LogicException('Registros de auditoria não podem ser alterados.');
        });

        self::deleting(static function (): never {
            throw new LogicException('Registros de auditoria não podem ser removidos pelo modelo.');
        });
    }

    protected function casts(): array
    {
        return [
            'metadata' => 'encrypted:array',
            'occurred_at' => 'immutable_datetime',
        ];
    }
}
