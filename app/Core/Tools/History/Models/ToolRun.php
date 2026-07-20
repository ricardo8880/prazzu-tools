<?php

namespace App\Core\Tools\History\Models;

use App\Core\Tools\History\Enums\ToolRunStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ToolRun extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'tool_slug',
        'tool_version',
        'schema_version',
        'rule_version',
        'reference_date',
        'status',
        'input_payload',
        'result_payload',
        'normative_references',
        'error_code',
        'started_at',
        'finished_at',
        'expires_at',
    ];

    /** @return HasMany<ToolRunFavorite, $this> */
    public function favorites(): HasMany
    {
        return $this->hasMany(ToolRunFavorite::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ToolRunStatus::class,
            'schema_version' => 'integer',
            'reference_date' => 'immutable_date',
            'input_payload' => 'encrypted:array',
            'result_payload' => 'encrypted:array',
            'normative_references' => 'encrypted:array',
            'started_at' => 'immutable_datetime',
            'finished_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
        ];
    }
}
