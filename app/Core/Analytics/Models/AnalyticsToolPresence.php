<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

final class AnalyticsToolPresence extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'tool_slug', 'visitor_id', 'analytics_session_id', 'user_id',
        'path', 'source', 'country_code', 'region', 'city', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }
}
