<?php

namespace App\Core\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AnalyticsVisitor extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'user_id', 'first_seen_at', 'last_seen_at', 'first_source',
        'first_medium', 'first_campaign', 'first_utm', 'last_source', 'last_medium',
        'last_campaign', 'last_utm', 'first_referrer', 'last_referrer', 'properties',
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'first_utm' => 'array',
            'last_utm' => 'array',
            'properties' => 'array',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(PlatformAnalyticsEvent::class, 'visitor_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AnalyticsSession::class, 'visitor_id');
    }
}
