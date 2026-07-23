<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;

final class AnalyticsMetricSql
{
    public static function identity(string $scope = "''"): string
    {
        $driver = PlatformAnalyticsEvent::query()->getConnection()->getDriverName();
        $parts = [
            "COALESCE(CAST(analytics_session_id AS CHAR), '')",
            "COALESCE(visitor_id, '')",
            "COALESCE(CAST(user_id AS CHAR), '')",
            $scope,
        ];

        $knownIdentity = match ($driver) {
            'pgsql' => implode(" || '|' || ", array_map(fn (string $part): string => str_replace(' AS CHAR', ' AS TEXT', $part), $parts)),
            'sqlite' => implode(" || '|' || ", array_map(fn (string $part): string => str_replace(' AS CHAR', ' AS TEXT', $part), $parts)),
            default => "CONCAT_WS('|', ".implode(', ', $parts).')',
        };

        $eventIdentity = match ($driver) {
            'pgsql', 'sqlite' => 'CAST(event_id AS TEXT)',
            default => 'CAST(event_id AS CHAR)',
        };

        return 'CASE WHEN analytics_session_id IS NULL AND visitor_id IS NULL AND user_id IS NULL '
            .'THEN '.$eventIdentity.' ELSE '.$knownIdentity.' END';
    }

    /** @param list<string> $events */
    public static function countDistinctCase(array $events, string $scope = "''"): string
    {
        $placeholders = implode(',', array_fill(0, max(1, count($events)), '?'));

        return 'COUNT(DISTINCT CASE WHEN event_name IN ('.$placeholders.') THEN '.self::identity($scope).' END)';
    }
}
