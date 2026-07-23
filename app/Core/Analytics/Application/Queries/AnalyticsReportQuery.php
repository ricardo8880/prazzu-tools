<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class AnalyticsReportQuery
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}

    /** @param array<string, mixed> $filters */
    public function execute(AnalyticsPeriod $period, array $filters, int $limit = 100): array
    {
        $current = $this->filtered($period, $filters);
        $previous = $this->filtered($period->previous(), $filters);

        return [
            'period' => $period,
            'previous_period' => $period->previous(),
            'summary' => $this->comparison($this->summary(clone $current), $this->summary(clone $previous)),
            'rows' => (clone $current)->latest('occurred_at')->limit($limit)->get(),
            'total_rows' => (clone $current)->count(),
            'dimensions' => $this->dimensions(),
            'complete_report' => $this->strategic($period, $filters),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function strategic(AnalyticsPeriod $period, array $filters): array
    {
        $current = $this->filtered($period, $filters);
        $previous = $this->filtered($period->previous(), $filters);
        $conversionEvents = $this->eventNames->expand(config('analytics.dashboard.conversion_events', []));

        return [
            'summary' => $this->comparison($this->summary(clone $current), $this->summary(clone $previous)),
            'event_breakdown' => (clone $current)->select('platform_analytics_events.event_name', DB::raw('COUNT(*) as total'))->groupBy('platform_analytics_events.event_name')->orderByDesc('total')->get()->map->toArray()->all(),
            'tool_breakdown' => $this->breakdown(clone $current, 'subject_slug', $conversionEvents, fn (Builder $query) => $query->where('platform_analytics_events.subject_type', 'tool')),
            'channel_breakdown' => $this->breakdown(clone $current, 'channel', $conversionEvents),
            'source_breakdown' => $this->breakdown(clone $current, 'source', $conversionEvents),
            'device_breakdown' => $this->breakdown(clone $current, 'device_type', $conversionEvents),
            'medium_breakdown' => $this->breakdown(clone $current, 'medium', $conversionEvents),
            'campaign_breakdown' => $this->breakdown(clone $current, 'campaign', $conversionEvents),
            'browser_breakdown' => $this->breakdown(clone $current, 'browser', $conversionEvents),
            'os_breakdown' => $this->breakdown(clone $current, 'operating_system', $conversionEvents),
            'language_breakdown' => $this->breakdown(clone $current, 'language', $conversionEvents),
            'country_breakdown' => $this->breakdown(clone $current, 'country_code', $conversionEvents),
            'region_breakdown' => $this->breakdown(clone $current, 'region', $conversionEvents),
            'city_breakdown' => $this->breakdown(clone $current, 'city', $conversionEvents),
            'path_breakdown' => $this->breakdown(clone $current, 'path', $conversionEvents),
            'referrer_breakdown' => $this->breakdown(clone $current, 'referrer', $conversionEvents),
            'subject_type_breakdown' => $this->breakdown(clone $current, 'subject_type', $conversionEvents),
            'acquisition_context_breakdown' => $this->breakdown(clone $current, 'acquisition_context_id', $conversionEvents),
            'daily_series' => $this->timeSeries(clone $current, 'date'),
            'hourly_series' => $this->timeSeries(clone $current, 'hour'),
            'data_quality' => $this->dataQuality(clone $current),
            'tool_performance' => $this->toolPerformance($period, $filters),
            'funnel' => $this->funnel($period, $filters),
        ];
    }

    private function timeSeries(Builder $query, string $grain): array
    {
        $driver = DB::connection()->getDriverName();
        $bucket = match ([$driver, $grain]) {
            ['sqlite', 'hour'] => "strftime('%H', platform_analytics_events.occurred_at)",
            ['sqlite', 'date'] => "date(platform_analytics_events.occurred_at)",
            default => $grain === 'hour'
                ? 'HOUR(platform_analytics_events.occurred_at)'
                : 'DATE(platform_analytics_events.occurred_at)',
        };

        return $query->selectRaw($bucket.' as bucket')
            ->selectRaw('COUNT(*) as events')
            ->selectRaw('COUNT(DISTINCT platform_analytics_events.visitor_id) as visitors')
            ->selectRaw('COUNT(DISTINCT platform_analytics_events.analytics_session_id) as sessions')
            ->groupByRaw($bucket)
            ->orderBy('bucket')
            ->get()
            ->map(fn ($row): array => [
                'bucket' => (string) $row->bucket,
                'events' => (int) $row->events,
                'visitors' => (int) $row->visitors,
                'sessions' => (int) $row->sessions,
            ])->all();
    }

    private function dataQuality(Builder $query): array
    {
        $total = (clone $query)->count();
        $fields = [
            'visitor_id', 'analytics_session_id', 'channel', 'source', 'path', 'device_type',
            'browser', 'operating_system', 'country_code', 'region', 'city', 'language',
        ];
        $coverage = [];
        foreach ($fields as $field) {
            $present = (clone $query)->whereNotNull("platform_analytics_events.$field")
                ->where("platform_analytics_events.$field", '!=', '')
                ->count();
            $coverage[$field] = [
                'present' => $present,
                'missing' => max(0, $total - $present),
                'coverage_rate' => $this->rate($present, $total),
            ];
        }

        return [
            'total_events' => $total,
            'identified_events' => (clone $query)->where(function (Builder $q): void {
                $q->whereNotNull('platform_analytics_events.visitor_id')
                    ->orWhereNotNull('platform_analytics_events.analytics_session_id')
                    ->orWhereNotNull('platform_analytics_events.user_id');
            })->count(),
            'anonymous_events' => (clone $query)->whereNull('platform_analytics_events.visitor_id')
                ->whereNull('platform_analytics_events.analytics_session_id')
                ->whereNull('platform_analytics_events.user_id')->count(),
            'fields' => $coverage,
        ];
    }

    /** @param list<string> $conversionEvents */
    private function breakdown(Builder $query, string $column, array $conversionEvents, ?callable $scope = null): array
    {
        if ($scope !== null) {
            $scope($query);
        }

        return $query->selectRaw("COALESCE(platform_analytics_events.$column, '') as name")
            ->selectRaw('COUNT(*) as events')
            ->selectRaw('COUNT(DISTINCT platform_analytics_events.visitor_id) as visitors')
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($conversionEvents, "COALESCE(platform_analytics_events.$column, '')").' as conversions', $conversionEvents)
            ->groupBy("platform_analytics_events.$column")
            ->orderByDesc('events')
            ->limit(100)
            ->get()->map(fn ($row) => ['name' => (string) $row->name, 'events' => (int) $row->events, 'visitors' => (int) $row->visitors, 'conversions' => (int) $row->conversions])->all();
    }

    /** @param array<string, mixed> $filters */
    private function toolPerformance(AnalyticsPeriod $period, array $filters): array
    {
        $current = collect($this->toolPerformanceRows($this->filtered($period, $filters)))->keyBy('name');
        $previous = collect($this->toolPerformanceRows($this->filtered($period->previous(), $filters)))->keyBy('name');

        return $current->map(function (array $row, string $name) use ($previous): array {
            $before = $previous->get($name, []);
            $row['previous'] = $before;
            $row['completion_rate_delta_pp'] = isset($before['completion_rate'])
                ? round($row['completion_rate'] - $before['completion_rate'], 1)
                : null;
            $row['start_rate_delta_pp'] = isset($before['start_rate'])
                ? round($row['start_rate'] - $before['start_rate'], 1)
                : null;

            return $row;
        })->sortByDesc('completed')->values()->all();
    }

    private function toolPerformanceRows(Builder $query): array
    {
        $opened = $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolOpened);
        $started = $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolCalculationStarted);
        $completed = $this->eventNames->expand([AnalyticsEventName::ToolCalculationCompleted, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed]);
        $exported = $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolResultExported);

        return $query->where('platform_analytics_events.subject_type', 'tool')
            ->whereNotNull('platform_analytics_events.subject_slug')
            ->selectRaw('platform_analytics_events.subject_slug as name')
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($opened, "COALESCE(platform_analytics_events.subject_slug, '')").' as opened', $opened)
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($started, "COALESCE(platform_analytics_events.subject_slug, '')").' as started', $started)
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($completed, "COALESCE(platform_analytics_events.subject_slug, '')").' as completed', $completed)
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($exported, "COALESCE(platform_analytics_events.subject_slug, '')").' as exported', $exported)
            ->groupBy('platform_analytics_events.subject_slug')
            ->get()->map(function ($row): array {
                $opened = (int) $row->opened;
                $started = (int) $row->started;
                $completed = (int) $row->completed;
                $exported = (int) $row->exported;

                return [
                    'name' => (string) $row->name,
                    'opened' => $opened,
                    'started' => $started,
                    'completed' => $completed,
                    'exported' => $exported,
                    'start_rate' => $this->rate($started, $opened),
                    'completion_rate' => $this->rate($completed, $started),
                    'export_rate' => $this->rate($exported, $completed),
                ];
            })->all();
    }

    /** @param array<string, mixed> $filters */
    private function funnel(AnalyticsPeriod $period, array $filters): array
    {
        $current = $this->funnelValues($this->filtered($period, $filters));
        $previous = $this->funnelValues($this->filtered($period->previous(), $filters));

        return [
            'current' => $current,
            'previous' => $previous,
            'rates' => [
                'open_to_start' => $this->rate($current['started'], $current['opened']),
                'start_to_complete' => $this->rate($current['completed'], $current['started']),
                'complete_to_export' => $this->rate($current['exported'], $current['completed']),
            ],
            'previous_rates' => [
                'open_to_start' => $this->rate($previous['started'], $previous['opened']),
                'start_to_complete' => $this->rate($previous['completed'], $previous['started']),
                'complete_to_export' => $this->rate($previous['exported'], $previous['completed']),
            ],
        ];
    }

    private function funnelValues(Builder $query): array
    {
        $events = [
            'opened' => $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolOpened),
            'started' => $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolCalculationStarted),
            'completed' => $this->eventNames->expand([AnalyticsEventName::ToolCalculationCompleted, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed]),
            'exported' => $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolResultExported),
        ];

        $result = [];
        foreach ($events as $key => $names) {
            $result[$key] = $this->logicalCount(clone $query, $names, "COALESCE(platform_analytics_events.subject_slug, '')");
        }

        return $result;
    }

    /** @param list<string> $events */
    private function logicalCount(Builder $query, array $events, string $scope = "''"): int
    {
        return (int) ($query
            ->selectRaw(AnalyticsMetricSql::countDistinctCase($events, $scope).' as aggregate', $events)
            ->value('aggregate') ?? 0);
    }

    /** @param list<string> $values */
    private function placeholders(array $values): string
    {
        return implode(',', array_fill(0, max(1, count($values)), '?'));
    }

    private function rate(int $numerator, int $denominator): float
    {
        return $denominator === 0 ? 0.0 : round(($numerator / $denominator) * 100, 1);
    }

    /** @param array<string, mixed> $filters */
    public function rows(AnalyticsPeriod $period, array $filters, int $limit = 10000): Collection
    {
        return $this->filtered($period, $filters)->latest('occurred_at')->limit($limit)->get();
    }

    /** @param array<string, mixed> $filters */
    private function filtered(AnalyticsPeriod $period, array $filters): Builder
    {
        $query = PlatformAnalyticsEvent::query()
            ->whereBetween('platform_analytics_events.occurred_at', [$period->start, $period->end]);

        foreach (['channel', 'source', 'city', 'region', 'device_type', 'operating_system'] as $field) {
            if (($filters[$field] ?? null) !== null && $filters[$field] !== '') {
                $query->where("platform_analytics_events.$field", $filters[$field]);
            }
        }

        if (! empty($filters['tool'])) {
            $query->where('platform_analytics_events.subject_type', 'tool')
                ->where('platform_analytics_events.subject_slug', $filters['tool']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('platform_analytics_events.user_id', (int) $filters['user_id']);
        }

        if (! empty($filters['event_name'])) {
            $query->whereIn('platform_analytics_events.event_name', $this->eventNames->acceptedNamesFor($filters['event_name']));
        }

        if (! empty($filters['category']) || ! empty($filters['author_id'])) {
            $query->join('blog_posts', function ($join): void {
                $join->on('blog_posts.id', '=', 'platform_analytics_events.subject_id')
                    ->where('platform_analytics_events.subject_type', '=', 'blog_post');
            });

            if (! empty($filters['category'])) {
                $query->where('blog_posts.category', $filters['category']);
            }
            if (! empty($filters['author_id'])) {
                $query->where('blog_posts.author_id', (int) $filters['author_id']);
            }

            $query->select('platform_analytics_events.*');
        }

        return $query;
    }

    /** @return array<string, int|float> */
    private function summary(Builder $query): array
    {
        return [
            'events' => (clone $query)->count(),
            'visitors' => (clone $query)->whereNotNull('platform_analytics_events.visitor_id')->distinct()->count('platform_analytics_events.visitor_id'),
            'sessions' => (clone $query)->whereNotNull('platform_analytics_events.analytics_session_id')->distinct()->count('platform_analytics_events.analytics_session_id'),
            'users' => (clone $query)->whereNotNull('platform_analytics_events.user_id')->distinct()->count('platform_analytics_events.user_id'),
            'conversions' => $this->logicalCount(clone $query, $this->eventNames->expand(config('analytics.dashboard.conversion_events', []))),
        ];
    }

    /** @param array<string, int|float> $current @param array<string, int|float> $previous */
    private function comparison(array $current, array $previous): array
    {
        $result = [];
        foreach ($current as $key => $value) {
            $before = $previous[$key] ?? 0;
            $change = $before == 0 ? ($value == 0 ? 0.0 : null) : round((($value - $before) / $before) * 100, 1);
            $result[$key] = ['value' => $value, 'previous' => $before, 'change' => $change];
        }

        return $result;
    }

    /** @return array<string, Collection> */
    private function dimensions(): array
    {
        $distinct = static fn (string $column): Collection => PlatformAnalyticsEvent::query()
            ->whereNotNull($column)->where($column, '!=', '')->distinct()->orderBy($column)->limit(250)->pluck($column);

        return [
            'channels' => $distinct('channel'),
            'sources' => $distinct('source'),
            'cities' => $distinct('city'),
            'regions' => $distinct('region'),
            'devices' => $distinct('device_type'),
            'operating_systems' => $distinct('operating_system'),
            'tools' => PlatformAnalyticsEvent::query()->where('subject_type', 'tool')->whereNotNull('subject_slug')->distinct()->orderBy('subject_slug')->pluck('subject_slug'),
            'events' => $distinct('event_name'),
            'categories' => DB::table('blog_posts')->distinct()->orderBy('category')->pluck('category'),
            'authors' => DB::table('users')->join('blog_posts', 'blog_posts.author_id', '=', 'users.id')->distinct()->orderBy('users.name')->pluck('users.name', 'users.id'),
        ];
    }
}
