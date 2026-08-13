<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Core\Tools\ToolCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class ToolAnalyticsQuery
{
    public function __construct(
        private ToolCatalog $catalog,
        private AnalyticsEventNameResolver $eventNames,
    ) {}

    /** @param array<string, string|null> $filters @return array<string, mixed> */
    public function overview(AnalyticsPeriod $period, array $filters = []): array
    {
        $current = $this->productMetrics($period, $filters)->keyBy('slug');
        $previous = $this->productMetrics($period->previous(), $filters)->keyBy('slug');

        $tools = $this->catalog->all(false)->map(function (array $tool) use ($current, $previous): object {
            $row = $current->get($tool['slug'], $this->emptyMetric($tool['slug']));
            $before = $previous->get($tool['slug'], $this->emptyMetric($tool['slug']));

            return (object) array_merge($tool, (array) $row, [
                'opens_trend' => $this->percentageChange($row->opens, $before->opens),
                'completion_trend' => $this->percentageChange($row->completion_rate, $before->completion_rate),
                'previous_completion_rate' => $before->completion_rate,
                'previous_abandonment_rate' => $before->abandonment_rate,
                'previous_errors' => $before->errors,
            ]);
        })->sortByDesc('opens')->values();

        $events = $this->filteredEvents($period, $filters)->get();

        return [
            'period' => $period,
            'previous_period' => $period->previous(),
            'filters' => $filters,
            'filter_options' => $this->filterOptions($period),
            'summary' => [
                'tools' => $tools->count(),
                'opens' => (int) $tools->sum('opens'),
                'results' => (int) $tools->sum('results'),
                'calculations' => (int) $tools->sum('calculations'),
                'abandonments' => (int) $tools->sum('abandonments'),
                'errors' => (int) $tools->sum('errors'),
                'exports' => (int) $tools->sum('exports'),
                'shares' => (int) $tools->sum('shares'),
            ],
            'tools' => $tools,
            'rankings' => [
                'most_opened' => $tools->sortByDesc('opens')->take(10)->values(),
                'highest_completion' => $tools->filter(fn (object $r) => $r->opens > 0)->sortByDesc('completion_rate')->take(10)->values(),
                'highest_abandonment' => $tools->filter(fn (object $r) => $r->starts > 0)->sortByDesc('abandonment_rate')->take(10)->values(),
                'most_errors' => $tools->sortByDesc('errors')->take(10)->values(),
            ],
            'problem_fields' => $this->problemFields($events),
            'dropoff_steps' => $this->dropoffSteps($events),
            'daily' => $this->daily($period, null, $filters),
            'alerts' => $this->alerts($tools),
        ];
    }

    /** @return array<string, mixed> */
    public function tool(string $slug, AnalyticsPeriod $period): array
    {
        $tool = $this->catalog->find($slug);
        abort_if($tool === null, 404);
        $metric = $this->productMetrics($period, ['tool' => $slug])->first() ?? $this->emptyMetric($slug);

        return [
            'period' => $period,
            'tool' => $tool,
            'metrics' => (object) array_merge($tool, (array) $metric),
            'daily' => $this->daily($period, $slug),
            'devices' => $this->audienceBreakdown($period, $slug, 'device_type', 'unknown'),
            'sources' => $this->audienceBreakdown($period, $slug, 'source', 'direct')->take(10),
            'recent_events' => $this->base($period, $slug)->latest('occurred_at')->limit(30)->get(['event_name', 'device_type', 'source', 'occurred_at']),
        ];
    }

    /** @param array<string, string|null> $filters */
    private function productMetrics(AnalyticsPeriod $period, array $filters): Collection
    {
        return $this->filteredEvents($period, $filters)->orderBy('occurred_at')->get()->groupBy('subject_slug')
            ->map(function (Collection $events, string $slug): object {
                $count = fn (AnalyticsEventName $name): int => $events->whereIn('event_name', $this->names([$name]))->count();
                $unique = fn (AnalyticsEventName $name): int => $this->uniqueAudienceCount($events, $name);
                $opens = $count(AnalyticsEventName::ToolOpened);
                $starts = $count(AnalyticsEventName::ToolStarted);
                $calculations = $count(AnalyticsEventName::ToolCalculationExecuted);
                $results = $count(AnalyticsEventName::ToolResultViewed);
                $abandonments = $count(AnalyticsEventName::ToolAbandoned);
                $errors = $count(AnalyticsEventName::ToolValidationError);
                $uniqueOpens = $unique(AnalyticsEventName::ToolOpened);
                $uniqueStarts = $unique(AnalyticsEventName::ToolStarted);
                $uniqueResults = $unique(AnalyticsEventName::ToolResultViewed);
                $uniqueAbandonments = $unique(AnalyticsEventName::ToolAbandoned);
                $uniqueExports = $unique(AnalyticsEventName::ToolResultExported);
                $uniqueShares = $unique(AnalyticsEventName::ToolShared);
                $calculationTimes = $this->elapsedTimes($events, AnalyticsEventName::ToolStarted, AnalyticsEventName::ToolCalculationExecuted);
                $abandonmentTimes = $this->elapsedTimes($events, AnalyticsEventName::ToolStarted, AnalyticsEventName::ToolAbandoned);

                return (object) [
                    'slug' => $slug,
                    'opens' => $opens,
                    'starts' => $starts,
                    'calculations' => $calculations,
                    'results' => $results,
                    'abandonments' => $abandonments,
                    'errors' => $errors,
                    'exports' => $count(AnalyticsEventName::ToolResultExported),
                    'shares' => $count(AnalyticsEventName::ToolShared),
                    'fields_completed' => $count(AnalyticsEventName::ToolFieldCompleted),
                    'completion_rate' => $this->conversionRate($uniqueResults, $uniqueOpens),
                    'abandonment_rate' => $this->conversionRate($uniqueAbandonments, $uniqueStarts),
                    'export_rate' => $this->conversionRate($uniqueExports, $uniqueResults),
                    'share_rate' => $this->conversionRate($uniqueShares, $uniqueResults),
                    'average_calculation_seconds' => $this->average($calculationTimes),
                    'median_calculation_seconds' => $this->percentile($calculationTimes, 50),
                    'p95_calculation_seconds' => $this->percentile($calculationTimes, 95),
                    'average_abandonment_seconds' => $this->average($abandonmentTimes),
                ];
            })->values();
    }

    private function uniqueAudienceCount(Collection $events, AnalyticsEventName $name): int
    {
        return $events->whereIn('event_name', $this->names([$name]))
            ->map(fn ($event): string => (string) (
                $event->analytics_session_id
                ?: $event->visitor_id
                ?: $event->session_id
                ?: $event->user_id
                ?: $event->event_id
                ?: 'event-'.$event->id
            ))
            ->unique()
            ->count();
    }

    private function conversionRate(int $converted, int $base): float
    {
        if ($base <= 0) {
            return 0.0;
        }

        return round(min(100, $converted / $base * 100), 1);
    }

    private function emptyMetric(string $slug): object
    {
        return (object) [
            'slug' => $slug, 'opens' => 0, 'starts' => 0, 'calculations' => 0, 'results' => 0,
            'abandonments' => 0, 'errors' => 0, 'exports' => 0, 'shares' => 0, 'fields_completed' => 0,
            'completion_rate' => 0.0, 'abandonment_rate' => 0.0, 'export_rate' => 0.0, 'share_rate' => 0.0,
            'average_calculation_seconds' => 0.0, 'median_calculation_seconds' => 0.0,
            'p95_calculation_seconds' => 0.0, 'average_abandonment_seconds' => 0.0,
        ];
    }

    private function elapsedTimes(Collection $events, AnalyticsEventName $from, AnalyticsEventName $to): array
    {
        $fromNames = $this->names([$from]);
        $toNames = $this->names([$to]);
        $durations = [];

        foreach ($events->groupBy(fn ($event) => data_get($event->metadata, 'journey_id') ?: $event->analytics_session_id ?: $event->visitor_id ?: $event->session_id ?: 'anonymous') as $journey) {
            $start = $journey->first(fn ($event) => in_array($event->event_name, $fromNames, true));
            $end = $start ? $journey->first(fn ($event) => in_array($event->event_name, $toNames, true) && $event->occurred_at->greaterThanOrEqualTo($start->occurred_at)) : null;
            if ($start && $end) {
                $durations[] = $start->occurred_at->diffInSeconds($end->occurred_at);
            }
        }

        sort($durations);

        return $durations;
    }

    private function problemFields(Collection $events): Collection
    {
        return $events->filter(fn ($event) => in_array($event->event_name, $this->names([AnalyticsEventName::ToolValidationError, AnalyticsEventName::ToolAbandoned]), true))
            ->filter(fn ($event) => filled(data_get($event->metadata, 'field')))
            ->groupBy(fn ($event) => $event->subject_slug.'|'.data_get($event->metadata, 'field').'|'.data_get($event->metadata, 'step', '—'))
            ->map(function (Collection $rows): object {
                $first = $rows->first();

                return (object) [
                    'tool' => $first->subject_slug, 'field' => data_get($first->metadata, 'field'),
                    'step' => data_get($first->metadata, 'step', '—'),
                    'errors' => $rows->whereIn('event_name', $this->names([AnalyticsEventName::ToolValidationError]))->count(),
                    'abandonments' => $rows->whereIn('event_name', $this->names([AnalyticsEventName::ToolAbandoned]))->count(),
                ];
            })->sortByDesc(fn ($row) => $row->errors + $row->abandonments)->take(20)->values();
    }

    private function dropoffSteps(Collection $events): Collection
    {
        $abandoned = $events->whereIn('event_name', $this->names([AnalyticsEventName::ToolAbandoned]));
        $total = max(1, $abandoned->count());

        return $abandoned->groupBy(fn ($event) => data_get($event->metadata, 'step', 'unknown'))
            ->map(fn (Collection $rows, string $step) => (object) ['step' => $step, 'total' => $rows->count(), 'percentage' => round($rows->count() / $total * 100, 1)])
            ->sortByDesc('total')->values();
    }

    private function alerts(Collection $tools): Collection
    {
        return $tools->flatMap(function (object $tool): array {
            $alerts = [];
            if ($tool->completion_trend !== null && $tool->completion_trend <= -20) {
                $alerts[] = (object) ['severity' => 'danger', 'tool' => $tool->name, 'message' => 'Queda de conclusão superior a 20%.'];
            }
            if ($tool->previous_errors > 0 && $tool->errors > $tool->previous_errors * 1.2) {
                $alerts[] = (object) ['severity' => 'warning', 'tool' => $tool->name, 'message' => 'Crescimento anormal de erros.'];
            }
            if ($tool->opens >= 10 && $tool->completion_rate < 20) {
                $alerts[] = (object) ['severity' => 'warning', 'tool' => $tool->name, 'message' => 'Muitas aberturas e poucos resultados visualizados.'];
            }

            return $alerts;
        })->values();
    }

    /** @param array<string, string|null> $filters */
    private function filteredEvents(AnalyticsPeriod $period, array $filters): Builder
    {
        $query = $this->base($period)->whereNotNull('subject_slug');
        $columns = ['source', 'device_type', 'browser', 'country_code', 'language'];
        foreach ($columns as $column) {
            if (filled($filters[$column] ?? null)) {
                $query->where($column, $filters[$column]);
            }
        }
        if (filled($filters['tool'] ?? null)) {
            $query->where('subject_slug', $filters['tool']);
        }
        if (filled($filters['category'] ?? null)) {
            $slugs = $this->catalog->all(false)->where('category', $filters['category'])->pluck('slug');
            $query->whereIn('subject_slug', $slugs);
        }

        return $query;
    }

    private function filterOptions(AnalyticsPeriod $period): array
    {
        $base = $this->base($period);
        $distinct = fn (string $column): Collection => (clone $base)->whereNotNull($column)->where($column, '!=', '')->distinct()->orderBy($column)->pluck($column);

        return [
            'tools' => $this->catalog->all(false)->map(fn (array $tool) => ['slug' => $tool['slug'], 'name' => $tool['name']]),
            'categories' => $this->catalog->all(false)->pluck('category')->filter()->unique()->sort()->values(),
            'sources' => $distinct('source'), 'devices' => $distinct('device_type'), 'browsers' => $distinct('browser'),
            'countries' => $distinct('country_code'), 'languages' => $distinct('language'),
        ];
    }

    private function audienceBreakdown(AnalyticsPeriod $period, string $slug, string $column, string $fallback): Collection
    {
        $events = $this->names([AnalyticsEventName::ToolOpened]);

        return $this->base($period, $slug)->whereIn('event_name', $events)->selectRaw("COALESCE($column, ?) as label", [$fallback])
            ->selectRaw('COUNT(*) as total')->groupBy($column)->orderByDesc('total')->get();
    }

    /** @param array<string, string|null> $filters */
    private function daily(AnalyticsPeriod $period, ?string $slug = null, array $filters = []): Collection
    {
        if ($slug) {
            $filters['tool'] = $slug;
        }

        return $this->filteredEvents($period, $filters)->selectRaw('DATE(occurred_at) as day')
            ->selectRaw($this->sumCase([AnalyticsEventName::ToolOpened]).' as opens', $this->names([AnalyticsEventName::ToolOpened]))
            ->selectRaw($this->sumCase([AnalyticsEventName::ToolResultViewed]).' as results', $this->names([AnalyticsEventName::ToolResultViewed]))
            ->selectRaw($this->sumCase([AnalyticsEventName::ToolAbandoned]).' as abandonments', $this->names([AnalyticsEventName::ToolAbandoned]))
            ->groupBy('day')->orderBy('day')->get();
    }

    private function base(AnalyticsPeriod $period, ?string $slug = null): Builder
    {
        return PlatformAnalyticsEvent::query()->where('channel', 'tool')->whereBetween('occurred_at', [$period->start, $period->end])
            ->when($slug, fn (Builder $q) => $q->where('subject_slug', $slug));
    }

    /** @param list<AnalyticsEventName> $events @return list<string> */
    private function names(array $events): array
    {
        return $this->eventNames->expand($events);
    }

    /** @param list<AnalyticsEventName> $events */
    private function sumCase(array $events): string
    {
        return 'SUM(CASE WHEN event_name IN ('.implode(',', array_fill(0, count($this->names($events)), '?')).') THEN 1 ELSE 0 END)';
    }

    private function average(array $values): float
    {
        return $values === [] ? 0.0 : round(array_sum($values) / count($values), 1);
    }

    private function percentile(array $values, int $percentile): float
    {
        if ($values === []) {
            return 0.0;
        }
        $index = ($percentile / 100) * (count($values) - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);

        return round($values[$lower] + (($values[$upper] - $values[$lower]) * ($index - $lower)), 1);
    }

    private function percentageChange(float|int $current, float|int $previous): ?float
    {
        if ((float) $previous === 0.0) {
            return (float) $current === 0.0 ? 0.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
