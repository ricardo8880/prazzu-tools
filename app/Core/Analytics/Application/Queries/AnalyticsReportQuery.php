<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Queries;

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
        ];
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
            'conversions' => (clone $query)->whereIn('platform_analytics_events.event_name', $this->eventNames->expand(config('analytics.dashboard.conversion_events', [])))->count(),
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
