<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsToolPresence;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class RealtimeAnalyticsQuery
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}

    private const ONLINE_WINDOW_MINUTES = 5;

    private const TOOL_PRESENCE_SECONDS = 25;

    /** @return array<string, mixed> */
    public function execute(?CarbonImmutable $now = null): array
    {
        $now ??= now()->toImmutable();
        $onlineSince = $now->subMinutes(self::ONLINE_WINDOW_MINUTES);
        $activitySince = $now->subMinutes(30);
        $toolPresenceSince = $now->subSeconds(self::TOOL_PRESENCE_SECONDS);

        $onlineSessions = AnalyticsSession::query()
            ->whereBetween('last_activity_at', [$onlineSince, $now]);
        $recentEvents = PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$activitySince, $now]);
        $activeToolPresences = AnalyticsToolPresence::query()
            ->whereBetween('last_seen_at', [$toolPresenceSince, $now]);

        $summary = [
            'online_users' => (clone $onlineSessions)->count(),
            'online_visitors' => (clone $onlineSessions)->whereNotNull('visitor_id')->distinct()->count('visitor_id'),
            'identified_users' => (clone $onlineSessions)->whereNotNull('user_id')->distinct()->count('user_id'),
            'open_pages' => (clone $onlineSessions)->whereNotNull('landing_path')->distinct()->count('landing_path'),
            'open_tools' => (clone $activeToolPresences)->count(),
            'events_30m' => (clone $recentEvents)->count(),
            'conversions_30m' => $this->logicalCount(clone $recentEvents, $this->conversionEvents()),
            'registrations_30m' => $this->logicalCount(clone $recentEvents, $this->eventNames->expand([AnalyticsEventName::AccountCreated])),
            'exports_30m' => $this->logicalCount(clone $recentEvents, [...$this->eventNames->expand([AnalyticsEventName::ToolResultExported, AnalyticsEventName::BusinessDocumentValidatorBatchExported]), 'file.downloaded'], "COALESCE(subject_slug, path, '')"),
        ];

        return [
            'generated_at' => $now,
            'online_window_minutes' => self::ONLINE_WINDOW_MINUTES,
            'activity_window_minutes' => 30,
            'summary' => $summary,
            'pages' => $this->onlinePages($onlineSessions),
            'tools' => $this->activeTools($activeToolPresences),
            'sources' => $this->sources($onlineSessions),
            'locations' => $this->locations($onlineSessions),
            'events' => (clone $recentEvents)->latest('occurred_at')->limit(50)->get([
                'event_name', 'channel', 'subject_slug', 'path', 'source', 'device_type', 'region', 'city', 'occurred_at',
            ]),
        ];
    }

    /** @return list<string> */
    private function conversionEvents(): array
    {
        return [
            'conversion.completed',
            ...$this->eventNames->expand([
                AnalyticsEventName::AccountCreated,
                AnalyticsEventName::SubscriptionCreated,
                AnalyticsEventName::ToolCalculationCompleted,
                AnalyticsEventName::BusinessDocumentValidatorBatchProcessed,
            ]),
        ];
    }

    /** @param list<string> $events */
    private function logicalCount(Builder $query, array $events, string $scope = "''"): int
    {
        return (int) ($query
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($events, $scope).' as aggregate', $events)
            ->value('aggregate') ?? 0);
    }

    private function onlinePages(Builder $sessions): Collection
    {
        return (clone $sessions)
            ->selectRaw("COALESCE(landing_path, '/') as label, COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors")
            ->groupBy('landing_path')->orderByDesc('total')->limit(15)->get();
    }

    private function activeTools(Builder $presences): Collection
    {
        return (clone $presences)
            ->selectRaw('tool_slug as label, COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('tool_slug')->orderByDesc('total')->limit(15)->get();
    }

    private function sources(Builder $sessions): Collection
    {
        return (clone $sessions)
            ->selectRaw("COALESCE(source, 'direct') as label, COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors")
            ->groupBy('source')->orderByDesc('total')->limit(12)->get();
    }

    private function locations(Builder $sessions): Collection
    {
        return (clone $sessions)
            ->selectRaw("COALESCE(country_code, 'unknown') as country, COALESCE(region, 'unknown') as region, COALESCE(city, 'unknown') as city, COUNT(*) as total")
            ->groupBy('country_code', 'region', 'city')->orderByDesc('total')->limit(20)->get();
    }
}
