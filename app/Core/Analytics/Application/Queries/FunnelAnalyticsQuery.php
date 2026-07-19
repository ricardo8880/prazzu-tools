<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsFunnel;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;

final class FunnelAnalyticsQuery
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}

    /** @param array<string,string|null> $filters */
    public function execute(AnalyticsPeriod $period, array $filters = []): array
    {
        $definitions = collect(config('analytics.funnels.standard', []))
            ->map(fn (array $funnel, string $key) => (object) ['key' => 'standard:'.$key, 'name' => $funnel['name'], 'description' => $funnel['description'] ?? null, 'identity_type' => $funnel['identity_type'] ?? 'visitor', 'steps' => collect($funnel['steps']), 'custom' => false]);

        $custom = AnalyticsFunnel::query()->with('steps')->where('is_active', true)->orderBy('name')->get()
            ->map(fn (AnalyticsFunnel $funnel) => (object) ['key' => 'custom:'.$funnel->id, 'id' => $funnel->id, 'name' => $funnel->name, 'description' => $funnel->description, 'identity_type' => $funnel->identity_type, 'steps' => $funnel->steps->map(fn ($step) => ['name' => $step->name, 'events' => $step->event_names]), 'custom' => true]);

        $definitions = $definitions->concat($custom)->values();
        $selectedKey = $filters['funnel'] ?? $definitions->first()?->key;
        $selected = $definitions->firstWhere('key', $selectedKey) ?? $definitions->first();

        $comparison = $definitions->map(function (object $funnel) use ($period, $filters): object {
            $result = $this->calculate($funnel, $period, $filters);

            return (object) [
                'key' => $funnel->key,
                'name' => $funnel->name,
                'custom' => $funnel->custom,
                'entrants' => $result['entrants'],
                'completed' => $result['completed'],
                'conversion_rate' => $result['conversion_rate'],
                'steps_count' => $result['steps']->count(),
            ];
        })->sortByDesc('conversion_rate')->values();

        return [
            'period' => $period,
            'funnels' => $definitions,
            'selected_funnel' => $selected,
            'result' => $selected ? $this->calculate($selected, $period, $filters) : null,
            'funnel_comparison' => $comparison,
            'sources' => PlatformAnalyticsEvent::query()->whereBetween('occurred_at', [$period->start, $period->end])->whereNotNull('source')->distinct()->orderBy('source')->pluck('source'),
        ];
    }

    /** @param object $funnel @param array<string,string|null> $filters */
    private function calculate(object $funnel, AnalyticsPeriod $period, array $filters): array
    {
        $steps = collect($funnel->steps)->values();
        $steps = $steps->map(fn (array $step): array => [...$step, 'events' => $this->eventNames->expand($step['events'])]);
        $eventNames = $steps->flatMap(fn ($step) => $step['events'])->unique()->values();
        $identityColumn = match ($funnel->identity_type) {
            'session' => 'analytics_session_id', 'user' => 'user_id', default => 'visitor_id'
        };

        $query = PlatformAnalyticsEvent::query()
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->whereIn('event_name', $eventNames)
            ->whereNotNull($identityColumn)
            ->orderBy('occurred_at');

        foreach (['source', 'device_type', 'subject_slug'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $groups = $query->get([$identityColumn, 'event_name', 'occurred_at'])->groupBy($identityColumn);
        $counts = array_fill(0, $steps->count(), 0);

        foreach ($groups as $events) {
            $cursor = null;
            foreach ($steps as $index => $step) {
                $matched = $events->first(function ($event) use ($step, $cursor): bool {
                    return in_array($event->event_name, $step['events'], true) && ($cursor === null || $event->occurred_at->greaterThanOrEqualTo($cursor));
                });
                if (! $matched) {
                    break;
                }
                $counts[$index]++;
                $cursor = $matched->occurred_at;
                $events = $events->filter(fn ($event) => $event->occurred_at->greaterThanOrEqualTo($cursor));
            }
        }

        $rows = $steps->map(function (array $step, int $index) use ($counts): object {
            $previous = $index === 0 ? $counts[0] : $counts[$index - 1];

            return (object) [
                'name' => $step['name'], 'events' => $step['events'], 'total' => $counts[$index],
                'step_rate' => $previous > 0 ? round($counts[$index] / $previous * 100, 1) : 0.0,
                'overall_rate' => $counts[0] > 0 ? round($counts[$index] / $counts[0] * 100, 1) : 0.0,
                'dropoff' => max(0, $previous - $counts[$index]),
            ];
        });

        return ['steps' => $rows, 'entrants' => $counts[0] ?? 0, 'completed' => end($counts) ?: 0, 'conversion_rate' => ($counts[0] ?? 0) > 0 ? round((end($counts) ?: 0) / $counts[0] * 100, 1) : 0.0, 'identities' => $groups->count()];
    }
}
