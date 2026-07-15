<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class AcquisitionAnalyticsQuery
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}
    /** @return array<string, mixed> */
    public function execute(AnalyticsPeriod $period): array
    {
        return [
            'period' => $period,
            'summary' => $this->summary($period),
            'channels' => $this->channels($period),
            'sources' => $this->sources($period),
            'campaigns' => $this->campaigns($period),
            'first_touch' => $this->visitorAttribution($period, 'first'),
            'last_touch' => $this->visitorAttribution($period, 'last'),
            'conversion_sources' => $this->eventAttribution($period, $this->events('conversion_events')),
            'registration_sources' => $this->eventAttribution($period, $this->events('registration_events')),
            'subscription_sources' => $this->eventAttribution($period, $this->events('subscription_events')),
            'export_sources' => $this->eventAttribution($period, $this->events('export_events')),
            'funnels' => $this->funnels($period),
        ];
    }

    /** @return array{sessions:int,visitors:int,sources:int,campaigns:int,conversions:int,conversion_rate:float} */
    private function summary(AnalyticsPeriod $period): array
    {
        $sessions = $this->sessionsIn($period);
        $sessionCount = (clone $sessions)->count();
        $conversions = $this->eventsIn($period)->whereIn('event_name', $this->events('conversion_events'))->count();

        return [
            'sessions' => $sessionCount,
            'visitors' => (clone $sessions)->distinct()->count('visitor_id'),
            'sources' => (clone $sessions)->whereNotNull('source')->distinct()->count('source'),
            'campaigns' => (clone $sessions)->whereNotNull('campaign')->where('campaign', '!=', '')->distinct()->count('campaign'),
            'conversions' => $conversions,
            'conversion_rate' => $sessionCount === 0 ? 0.0 : round(($conversions / $sessionCount) * 100, 1),
        ];
    }

    /** @return Collection<int, object> */
    private function channels(AnalyticsPeriod $period): Collection
    {
        return $this->sessionsIn($period)
            ->selectRaw("COALESCE(NULLIF(medium, ''), 'unknown') as medium")
            ->selectRaw('COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('medium')
            ->orderByDesc('sessions')
            ->get()
            ->map(fn (object $row): object => $this->appendConversions($row, $period, 'medium'));
    }

    /** @return Collection<int, object> */
    private function sources(AnalyticsPeriod $period): Collection
    {
        return $this->sessionsIn($period)
            ->selectRaw("COALESCE(NULLIF(source, ''), 'unknown') as source")
            ->selectRaw("COALESCE(NULLIF(medium, ''), 'unknown') as medium")
            ->selectRaw('COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('source', 'medium')
            ->orderByDesc('sessions')
            ->limit(50)
            ->get()
            ->map(fn (object $row): object => $this->appendConversions($row, $period, 'source'));
    }

    /** @return Collection<int, object> */
    private function campaigns(AnalyticsPeriod $period): Collection
    {
        return $this->sessionsIn($period)
            ->whereNotNull('campaign')->where('campaign', '!=', '')
            ->selectRaw('campaign, source, medium, COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('campaign', 'source', 'medium')
            ->orderByDesc('sessions')
            ->limit(30)
            ->get()
            ->map(function (object $row) use ($period): object {
                $conversions = $this->eventsIn($period)
                    ->where('campaign', $row->campaign)
                    ->whereIn('event_name', $this->events('conversion_events'))
                    ->count();
                $row->conversions = $conversions;
                $row->conversion_rate = (int) $row->sessions === 0 ? 0.0 : round(($conversions / (int) $row->sessions) * 100, 1);
                return $row;
            });
    }

    /** @return Collection<int, object> */
    private function visitorAttribution(AnalyticsPeriod $period, string $touch): Collection
    {
        $date = $touch === 'first' ? 'first_seen_at' : 'last_seen_at';
        $source = $touch.'_source';
        $medium = $touch.'_medium';

        return AnalyticsVisitor::query()
            ->whereBetween($date, [$period->start, $period->end])
            ->selectRaw("COALESCE(NULLIF($source, ''), 'unknown') as source")
            ->selectRaw("COALESCE(NULLIF($medium, ''), 'unknown') as medium")
            ->selectRaw('COUNT(*) as visitors')
            ->groupBy('source', 'medium')
            ->orderByDesc('visitors')
            ->limit(15)
            ->get();
    }

    /** @param list<string> $eventNames @return Collection<int, object> */
    private function eventAttribution(AnalyticsPeriod $period, array $eventNames): Collection
    {
        if ($eventNames === []) {
            return collect();
        }

        return $this->eventsIn($period)
            ->whereIn('event_name', $eventNames)
            ->selectRaw("COALESCE(NULLIF(source, ''), 'unknown') as source")
            ->selectRaw("COALESCE(NULLIF(medium, ''), 'unknown') as medium")
            ->selectRaw('COUNT(*) as total, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('source', 'medium')
            ->orderByDesc('total')
            ->limit(15)
            ->get();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function funnels(AnalyticsPeriod $period): Collection
    {
        $steps = (array) config('analytics.acquisition.funnel_steps', []);

        return $this->sources($period)->take(12)->map(function (object $source) use ($period, $steps): array {
            $sessionVisitorIds = $this->sessionsIn($period)->where('source', $source->source)->pluck('visitor_id')->filter()->unique()->values();
            $previous = null;
            $result = [];

            foreach ($steps as $key => $definition) {
                $events = $this->eventNames->expand(array_values(array_filter((array) ($definition['events'] ?? []), 'is_string')));
                $count = $events === [] ? 0 : PlatformAnalyticsEvent::query()
                    ->whereBetween('occurred_at', [$period->start, $period->end])
                    ->whereIn('visitor_id', $sessionVisitorIds)
                    ->whereIn('event_name', $events)
                    ->distinct()->count('visitor_id');
                $result[] = [
                    'key' => (string) $key,
                    'label' => (string) ($definition['label'] ?? $key),
                    'visitors' => $count,
                    'step_rate' => $previous === null ? 100.0 : ($previous === 0 ? 0.0 : round(($count / $previous) * 100, 1)),
                ];
                $previous = $count;
            }

            return ['source' => $source->source, 'medium' => $source->medium, 'steps' => $result];
        });
    }

    private function appendConversions(object $row, AnalyticsPeriod $period, string $field): object
    {
        $value = $row->{$field};
        $conversions = $this->eventsIn($period)
            ->where($field, $value === 'unknown' ? null : $value)
            ->whereIn('event_name', $this->events('conversion_events'))
            ->count();
        $row->conversions = $conversions;
        $row->conversion_rate = (int) $row->sessions === 0 ? 0.0 : round(($conversions / (int) $row->sessions) * 100, 1);

        return $row;
    }

    private function sessionsIn(AnalyticsPeriod $period): Builder
    {
        return AnalyticsSession::query()->whereBetween('started_at', [$period->start, $period->end]);
    }

    private function eventsIn(AnalyticsPeriod $period): Builder
    {
        return PlatformAnalyticsEvent::query()->whereBetween('occurred_at', [$period->start, $period->end]);
    }

    /** @return list<string> */
    private function events(string $key): array
    {
        return $this->eventNames->expand(array_values(array_filter((array) config("analytics.dashboard.$key", []), 'is_string')));
    }
}
