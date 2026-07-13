<?php

namespace App\Core\Usage\Models;

use Illuminate\Database\Eloquent\Model;

final class ToolUsageEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tool_slug', 'user_id', 'organization_id', 'event', 'duration_ms', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'immutable_datetime',
            'duration_ms' => 'integer',
        ];
    }
}
