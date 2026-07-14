<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

final class PlatformAnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_name', 'channel', 'subject_type', 'subject_id', 'subject_slug',
        'user_id', 'session_id', 'path', 'referrer', 'metadata', 'occurred_at',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
