<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AnalyticsSession extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'visitor_id', 'user_id', 'started_at', 'last_activity_at',
        'ended_at', 'landing_url', 'landing_path', 'referrer', 'source',
        'medium', 'campaign', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term',
        'utm_content', 'device_type', 'browser', 'operating_system',
        'language', 'timezone', 'screen_resolution', 'country_code', 'region', 'city', 'properties',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
            'properties' => 'array',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(PlatformAnalyticsEvent::class, 'analytics_session_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitor::class, 'visitor_id');
    }
}
