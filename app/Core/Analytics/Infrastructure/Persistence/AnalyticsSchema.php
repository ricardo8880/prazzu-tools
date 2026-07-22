<?php

declare(strict_types=1);

namespace App\Core\Analytics\Infrastructure\Persistence;

use Illuminate\Support\Facades\Schema;
use Throwable;

final class AnalyticsSchema
{
    private const TABLE_COLUMNS = [
        'analytics_visitors' => [
            'id', 'user_id', 'first_seen_at', 'last_seen_at',
            'first_source', 'first_medium', 'first_campaign', 'first_utm', 'first_referrer',
            'last_source', 'last_medium', 'last_campaign', 'last_utm', 'last_referrer',
        ],
        'analytics_sessions' => [
            'id', 'visitor_id', 'user_id', 'started_at', 'last_activity_at',
            'landing_url', 'landing_path', 'referrer', 'source', 'medium', 'campaign',
            'acquisition_context_id', 'acquisition_keyword', 'acquisition_campaign_identifier',
            'acquisition_primary_tool_slug', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'device_type', 'browser', 'operating_system', 'language', 'timezone',
            'screen_resolution', 'country_code', 'region', 'city',
        ],
        'platform_analytics_events' => [
            'event_id', 'event_name', 'schema_version', 'channel',
            'subject_type', 'subject_id', 'subject_slug', 'visitor_id', 'analytics_session_id',
            'user_id', 'session_id', 'url', 'path', 'referrer', 'source', 'medium', 'campaign',
            'acquisition_context_id', 'acquisition_keyword', 'acquisition_campaign_identifier',
            'acquisition_primary_tool_slug', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'device_type', 'browser', 'operating_system', 'language', 'timezone',
            'screen_resolution', 'country_code', 'region', 'city', 'ip_hash', 'user_agent',
            'metadata', 'occurred_at',
        ],
    ];

    public function isReady(): bool
    {
        try {
            foreach (self::TABLE_COLUMNS as $table => $columns) {
                if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                    return false;
                }
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
