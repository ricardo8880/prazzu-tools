<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Core\Feedback\Models\PageFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ExecutiveDashboardQuery
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}

    /** @return array<string, mixed> */
    public function execute(AnalyticsPeriod $period): array
    {
        $previous = $period->previous();
        $currentMetrics = $this->metrics($period);
        $previousMetrics = $this->metrics($previous);

        return [
            'period' => $period,
            'previous_period' => $previous,
            'metrics' => $this->withComparisons($currentMetrics, $previousMetrics),
            'visitor_snapshots' => $this->visitorSnapshots(),
            'daily' => $this->dailySeries($period),
            'top_sources' => $this->topSources($period),
            'top_pages' => $this->topPages($period),
            'search_demand' => $this->searchDemand($period),
            'page_feedback' => $this->pageFeedback($period),
            'recent_events' => PlatformAnalyticsEvent::query()
                ->whereBetween('occurred_at', [$period->start, $period->end])
                ->latest('occurred_at')
                ->limit(10)
                ->get(['event_name', 'channel', 'path', 'source', 'occurred_at']),
        ];
    }

    /** @return array{today:int,yesterday:int,last_7_days:int,last_30_days:int} */
    private function visitorSnapshots(): array
    {
        $yesterday = new AnalyticsPeriod(
            now()->toImmutable()->subDay()->startOfDay(),
            now()->toImmutable()->subDay()->endOfDay(),
        );

        return [
            'today' => $this->uniqueVisitors(AnalyticsPeriod::lastDays(1)),
            'yesterday' => $this->uniqueVisitors($yesterday),
            'last_7_days' => $this->uniqueVisitors(AnalyticsPeriod::lastDays(7)),
            'last_30_days' => $this->uniqueVisitors(AnalyticsPeriod::lastDays(30)),
        ];
    }

    private function uniqueVisitors(AnalyticsPeriod $period): int
    {
        return $this->eventsIn($period)->whereNotNull('visitor_id')->distinct()->count('visitor_id');
    }

    /** @return array<string, int|float> */
    private function metrics(AnalyticsPeriod $period): array
    {
        $events = $this->eventsIn($period);
        $pageViewEvents = $this->eventNames('page_view_events');
        $conversionEvents = $this->eventNames('conversion_events');
        $registrationEvents = $this->eventNames('registration_events');
        $subscriptionEvents = $this->eventNames('subscription_events');

        $sessions = AnalyticsSession::query()->whereBetween('started_at', [$period->start, $period->end]);
        $sessionCount = (clone $sessions)->count();
        $pageViews = $this->eventOccurrenceCount(clone $events, $pageViewEvents);

        return [
            'visitors' => (clone $events)->whereNotNull('visitor_id')->distinct()->count('visitor_id'),
            'users' => (clone $events)->whereNotNull('user_id')->distinct()->count('user_id'),
            'sessions' => $sessionCount,
            'page_views' => $pageViews,
            'average_session_seconds' => $this->averageSessionSeconds($sessions),
            'bounce_rate' => $this->bounceRate($period, $sessionCount, $pageViewEvents),
            'conversions' => $this->uniqueConvertedVisitors(clone $events, $conversionEvents),
            'registrations' => $this->logicalEventCount(clone $events, $registrationEvents),
            'subscriptions' => $this->logicalEventCount(clone $events, $subscriptionEvents),
            'estimated_revenue_cents' => $this->estimatedRevenueCents($period, $subscriptionEvents),
        ];
    }

    /**
     * @param array<string, int|float> $current
     * @param array<string, int|float> $previous
     * @return array<string, array{value:int|float, previous:int|float, change:?float}>
     */
    private function withComparisons(array $current, array $previous): array
    {
        $result = [];

        foreach ($current as $key => $value) {
            $previousValue = $previous[$key] ?? 0;
            $result[$key] = [
                'value' => $value,
                'previous' => $previousValue,
                'change' => $this->percentageChange((float) $value, (float) $previousValue),
            ];
        }

        return $result;
    }

    private function percentageChange(float $current, float $previous): ?float
    {
        if ($previous === 0.0) {
            return $current === 0.0 ? 0.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /** @return Collection<int, object{date:string,visitors:int,sessions:int,page_views:int,conversions:int}> */
    private function dailySeries(AnalyticsPeriod $period): Collection
    {
        $pageViewEvents = $this->eventNames('page_view_events');
        $conversionEvents = $this->eventNames('conversion_events');

        $eventRows = PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->selectRaw('DATE(occurred_at) as metric_date')
            ->selectRaw('COUNT(DISTINCT visitor_id) as visitors')
            ->selectRaw(AnalyticsMetricSql::countCase($pageViewEvents).' as page_views', $pageViewEvents)
            ->selectRaw(AnalyticsMetricSql::countDistinctVisitorsCase($conversionEvents).' as conversions', $conversionEvents)
            ->groupBy('metric_date')
            ->get()
            ->keyBy('metric_date');

        $sessionRows = AnalyticsSession::query()
            ->whereBetween('started_at', [$period->start, $period->end])
            ->selectRaw('DATE(started_at) as metric_date, COUNT(*) as sessions')
            ->groupBy('metric_date')
            ->pluck('sessions', 'metric_date');

        return collect(range(0, $period->days() - 1))->map(function (int $offset) use ($period, $eventRows, $sessionRows): object {
            $date = $period->start->addDays($offset)->toDateString();
            $event = $eventRows->get($date);

            return (object) [
                'date' => $date,
                'visitors' => (int) ($event?->visitors ?? 0),
                'sessions' => (int) ($sessionRows[$date] ?? 0),
                'page_views' => (int) ($event?->page_views ?? 0),
                'conversions' => (int) ($event?->conversions ?? 0),
            ];
        });
    }

    /** @return Collection<int, object> */
    private function topSources(AnalyticsPeriod $period): Collection
    {
        return $this->eventsIn($period)
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->selectRaw('source, COUNT(DISTINCT analytics_session_id) as sessions, COUNT(DISTINCT visitor_id) as visitors, COUNT(*) as events')
            ->groupBy('source')
            ->orderByDesc('sessions')
            ->limit(8)
            ->get();
    }

    /** @return Collection<int, object> */
    private function searchDemand(AnalyticsPeriod $period): Collection
    {
        return PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->where('event_name', 'home.search.submitted')
            ->latest('occurred_at')
            ->get(['metadata', 'occurred_at'])
            ->map(function (PlatformAnalyticsEvent $event): ?array {
                $query = trim((string) data_get($event->metadata, 'query', ''));

                if ($query === '') {
                    return null;
                }

                return [
                    'query' => $query,
                    'normalized' => mb_strtolower($query),
                    'result_count' => max(0, (int) data_get($event->metadata, 'result_count', 0)),
                    'has_results' => (bool) data_get($event->metadata, 'has_results', false),
                    'occurred_at' => $event->occurred_at,
                ];
            })
            ->filter()
            ->groupBy('normalized')
            ->map(function (Collection $rows): object {
                $first = $rows->first();

                return (object) [
                    'query' => $first['query'],
                    'searches' => $rows->count(),
                    'zero_result_searches' => $rows->where('has_results', false)->count(),
                    'last_result_count' => (int) $first['result_count'],
                    'has_results' => (bool) $first['has_results'],
                    'last_searched_at' => $first['occurred_at'],
                ];
            })
            ->sortBy([
                ['zero_result_searches', 'desc'],
                ['searches', 'desc'],
                ['last_searched_at', 'desc'],
            ])
            ->take(30)
            ->values();
    }

    /** @return array{count:int,average:float,recent:Collection<int, PageFeedback>} */
    private function pageFeedback(AnalyticsPeriod $period): array
    {
        $query = PageFeedback::query()->whereBetween('created_at', [$period->start, $period->end]);

        return [
            'count' => (clone $query)->count(),
            'average' => round((float) ((clone $query)->avg('rating') ?? 0), 2),
            'recent' => (clone $query)->latest('created_at')->limit(8)->get(['path', 'page_title', 'rating', 'comment', 'created_at']),
        ];
    }

    /** @return Collection<int, object> */
    private function topPages(AnalyticsPeriod $period): Collection
    {
        return $this->eventsIn($period)
            ->whereIn('event_name', $this->eventNames('page_view_events'))
            ->whereNotNull('path')
            ->selectRaw('path, COUNT(*) as views, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(8)
            ->get();
    }

    private function averageSessionSeconds(Builder $sessions): float
    {
        $driver = DB::connection()->getDriverName();
        $expression = match ($driver) {
            'sqlite' => '(julianday(COALESCE(ended_at, last_activity_at)) - julianday(started_at)) * 86400',
            'pgsql' => 'EXTRACT(EPOCH FROM (COALESCE(ended_at, last_activity_at) - started_at))',
            default => 'TIMESTAMPDIFF(SECOND, started_at, COALESCE(ended_at, last_activity_at))',
        };

        return round(max(0, (float) ((clone $sessions)->selectRaw("AVG($expression) as average_seconds")->value('average_seconds') ?? 0)));
    }

    /** @param list<string> $pageViewEvents */
    private function bounceRate(AnalyticsPeriod $period, int $sessionCount, array $pageViewEvents): float
    {
        if ($sessionCount === 0) {
            return 0.0;
        }

        $periodSessionIds = AnalyticsSession::query()
            ->whereBetween('started_at', [$period->start, $period->end])
            ->pluck('id');

        if ($periodSessionIds->isEmpty()) {
            return 0.0;
        }

        $meaningfulEvents = array_values(array_unique([
            ...$pageViewEvents,
            ...$this->eventNames('conversion_events'),
            ...$this->eventNames('export_events'),
            'tool.calculation.started',
            'blog.scroll.measured',
            'blog.time.spent',
            'blog.tool.clicked',
            'blog.reading.completed',
        ]));

        $events = PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->whereIn('analytics_session_id', $periodSessionIds)
            ->whereIn('event_name', $meaningfulEvents)
            ->get(['analytics_session_id', 'event_name']);

        $engagedSessions = $events->groupBy('analytics_session_id')->filter(function (Collection $sessionEvents) use ($pageViewEvents): bool {
            $pageViews = $sessionEvents->whereIn('event_name', $pageViewEvents)->count();
            if ($pageViews > 1) {
                return true;
            }

            return $sessionEvents->contains(fn (PlatformAnalyticsEvent $event): bool => ! in_array($event->event_name, $pageViewEvents, true));
        })->count();

        return round((($sessionCount - $engagedSessions) / $sessionCount) * 100, 1);
    }

    /** @param list<string> $subscriptionEvents */
    private function estimatedRevenueCents(AnalyticsPeriod $period, array $subscriptionEvents): int
    {
        $keys = config('analytics.dashboard.revenue_metadata_keys', []);

        return $this->eventsIn($period)
            ->whereIn('event_name', $subscriptionEvents)
            ->pluck('metadata')
            ->sum(function (mixed $metadata) use ($keys): int {
                if (is_string($metadata)) {
                    $metadata = json_decode($metadata, true);
                }

                $metadata = is_array($metadata) ? $metadata : [];

                foreach ($keys as $key) {
                    if (isset($metadata[$key]) && is_numeric($metadata[$key])) {
                        return max(0, (int) $metadata[$key]);
                    }
                }

                return 0;
            });
    }

    /** @param list<string> $events */
    private function eventOccurrenceCount(Builder $query, array $events): int
    {
        return (int) $query->whereIn('event_name', $events)->count();
    }

    /** @param list<string> $events */
    private function uniqueConvertedVisitors(Builder $query, array $events): int
    {
        return (int) ($query->selectRaw(AnalyticsMetricSql::countDistinctVisitorsCase($events).' as total', $events)->value('total') ?? 0);
    }

    /** @param list<string> $events */
    private function logicalEventCount(Builder $query, array $events, string $scope = "''"): int
    {
        return (int) ($query->selectRaw(AnalyticsMetricSql::countDistinctCase($events, $scope).' as total', $events)->value('total') ?? 0);
    }

    private function eventsIn(AnalyticsPeriod $period): Builder
    {
        return PlatformAnalyticsEvent::query()->whereBetween('occurred_at', [$period->start, $period->end]);
    }

    /** @return list<string> */
    private function eventNames(string $key): array
    {
        return $this->eventNames->expand(array_values(array_filter(config("analytics.dashboard.$key", []), 'is_string')));
    }

    /** @param list<string> $values */
    private function placeholders(array $values): string
    {
        return implode(',', array_fill(0, max(1, count($values)), '?'));
    }
}
