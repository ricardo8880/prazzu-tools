<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlatformAnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id', 'event_name', 'schema_version', 'channel', 'subject_type',
        'subject_id', 'subject_slug', 'visitor_id', 'analytics_session_id',
        'user_id', 'session_id', 'url', 'path', 'referrer', 'source', 'medium',
        'campaign', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term',
        'utm_content', 'device_type', 'browser', 'operating_system', 'language',
        'timezone', 'screen_resolution', 'country_code', 'region', 'city', 'ip_hash', 'user_agent',
        'metadata', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'immutable_datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitor::class, 'visitor_id');
    }

    public function analyticsSession(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSession::class, 'analytics_session_id');
    }
}
