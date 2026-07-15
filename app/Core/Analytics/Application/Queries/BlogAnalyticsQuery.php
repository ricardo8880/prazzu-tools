<?php

namespace App\Core\Analytics\Application\Queries;

use App\Blog\Models\BlogPost;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class BlogAnalyticsQuery
{
    /** @return array<string, mixed> */
    public function overview(AnalyticsPeriod $period): array
    {
        $posts = BlogPost::query()->with('author:id,name')->get();
        $metrics = $this->metricsByPost($period)->keyBy('post_id');
        $rows = $posts->map(fn (BlogPost $post): object => $this->postRow($post, $metrics->get((int) $post->getKey())));

        return [
            'period' => $period,
            'totals' => $this->totals($rows),
            'posts' => $rows->sortByDesc('views')->values(),
            'rankings' => [
                'most_read' => $rows->sortByDesc('views')->take(10)->values(),
                'most_shared' => $rows->sortByDesc('shares')->take(10)->values(),
                'longest_reading' => $rows->sortByDesc('average_time_seconds')->take(10)->values(),
                'highest_conversion' => $rows->filter(fn (object $row): bool => $row->views > 0)->sortByDesc('conversion_rate')->take(10)->values(),
                'highest_abandonment' => $rows->filter(fn (object $row): bool => $row->views > 0)->sortByDesc('abandonment_rate')->take(10)->values(),
                'never_accessed' => $rows->where('views', 0)->values(),
                'needs_update' => $rows->filter(fn (object $row): bool => $row->needs_update)->sortBy('updated_at')->values(),
            ],
            'categories' => $this->groupRows($rows, fn (object $row): string => $row->category ?: 'Sem categoria'),
            'authors' => $this->groupRows($rows, fn (object $row): string => $row->author ?: 'Sem autor'),
            'tags' => $this->tagRows($rows),
            'daily' => $this->dailySeries($period),
        ];
    }

    /** @return array<string, mixed> */
    public function post(BlogPost $post, AnalyticsPeriod $period): array
    {
        $metric = $this->metricsByPost($period, (int) $post->getKey())->first();
        $row = $this->postRow($post->loadMissing('author:id,name'), $metric);

        return [
            'period' => $period,
            'post' => $post,
            'metrics' => $row,
            'daily' => $this->dailySeries($period, (int) $post->getKey()),
            'tools' => $this->toolsForPost($period, (int) $post->getKey()),
            'recent_events' => $this->events($period, (int) $post->getKey())
                ->latest('occurred_at')->limit(20)
                ->get(['event_name', 'source', 'device_type', 'occurred_at']),
        ];
    }

    /** @return Collection<int, object> */
    private function metricsByPost(AnalyticsPeriod $period, ?int $postId = null): Collection
    {
        $seconds = $this->jsonNumber('seconds');
        $percentage = $this->jsonNumber('percentage');
        $query = $this->events($period, $postId)
            ->whereNotNull('subject_id')
            ->selectRaw('CAST(subject_id AS INTEGER) as post_id')
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_post_view' THEN 1 ELSE 0 END) as views")
            ->selectRaw("COUNT(DISTINCT CASE WHEN event_name = 'blog_post_view' THEN visitor_id END) as unique_visitors")
            ->selectRaw("COUNT(DISTINCT CASE WHEN event_name = 'blog_post_view' THEN analytics_session_id END) as sessions")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_reading_started' THEN 1 ELSE 0 END) as reading_starts")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_reading_completed' THEN 1 ELSE 0 END) as reading_completions")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_abandoned' THEN 1 ELSE 0 END) as abandonments")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_share' THEN 1 ELSE 0 END) as shares")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_download' THEN 1 ELSE 0 END) as downloads")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_comment' THEN 1 ELSE 0 END) as comments")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_tool_click' THEN 1 ELSE 0 END) as tool_clicks")
            ->selectRaw("SUM(CASE WHEN event_name IN ('account.created','user.registered') THEN 1 ELSE 0 END) as registrations")
            ->selectRaw("SUM(CASE WHEN event_name IN ('subscription.started','subscription.created','plus.subscribed') THEN 1 ELSE 0 END) as subscriptions")
            ->selectRaw("AVG(CASE WHEN event_name = 'blog_time_spent' THEN $seconds END) as average_time_seconds")
            ->selectRaw("MAX(CASE WHEN event_name = 'blog_time_spent' THEN $seconds END) as maximum_time_seconds")
            ->selectRaw("AVG(CASE WHEN event_name = 'blog_scroll' THEN $percentage END) as average_scroll")
            ->groupBy('subject_id');

        return $query->get();
    }

    private function postRow(BlogPost $post, ?object $metric): object
    {
        $views = (int) ($metric?->views ?? 0);
        $toolClicks = (int) ($metric?->tool_clicks ?? 0);
        $registrations = (int) ($metric?->registrations ?? 0);
        $subscriptions = (int) ($metric?->subscriptions ?? 0);
        $abandonments = (int) ($metric?->abandonments ?? 0);
        $updatedAt = $post->content_updated_at ?: $post->updated_at;

        return (object) [
            'id' => (int) $post->getKey(),
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'author' => $post->author?->name,
            'tags' => collect($post->related_keywords ?? [])->filter()->values(),
            'updated_at' => $updatedAt,
            'needs_update' => $updatedAt?->lt(now()->subMonths(12)) ?? true,
            'views' => $views,
            'unique_visitors' => (int) ($metric?->unique_visitors ?? 0),
            'sessions' => (int) ($metric?->sessions ?? 0),
            'reading_starts' => (int) ($metric?->reading_starts ?? 0),
            'reading_completions' => (int) ($metric?->reading_completions ?? 0),
            'average_time_seconds' => (int) round((float) ($metric?->average_time_seconds ?? 0)),
            'maximum_time_seconds' => (int) round((float) ($metric?->maximum_time_seconds ?? 0)),
            'average_scroll' => round((float) ($metric?->average_scroll ?? 0), 1),
            'reading_rate' => $views > 0 ? round(((int) ($metric?->reading_completions ?? 0) / $views) * 100, 1) : 0.0,
            'abandonments' => $abandonments,
            'abandonment_rate' => $views > 0 ? round(($abandonments / $views) * 100, 1) : 0.0,
            'shares' => (int) ($metric?->shares ?? 0),
            'downloads' => (int) ($metric?->downloads ?? 0),
            'comments' => (int) ($metric?->comments ?? 0),
            'tool_clicks' => $toolClicks,
            'ctr' => $views > 0 ? round(($toolClicks / $views) * 100, 1) : 0.0,
            'registrations' => $registrations,
            'subscriptions' => $subscriptions,
            'conversion_rate' => $views > 0 ? round((($registrations + $subscriptions) / $views) * 100, 1) : 0.0,
        ];
    }

    /** @param Collection<int, object> $rows @return array<string, int|float> */
    private function totals(Collection $rows): array
    {
        $views = (int) $rows->sum('views');
        $clicks = (int) $rows->sum('tool_clicks');

        return [
            'views' => $views,
            'unique_visitors' => (int) $rows->sum('unique_visitors'),
            'reading_completions' => (int) $rows->sum('reading_completions'),
            'shares' => (int) $rows->sum('shares'),
            'downloads' => (int) $rows->sum('downloads'),
            'tool_clicks' => $clicks,
            'registrations' => (int) $rows->sum('registrations'),
            'subscriptions' => (int) $rows->sum('subscriptions'),
            'ctr' => $views > 0 ? round(($clicks / $views) * 100, 1) : 0.0,
        ];
    }

    /** @param Collection<int, object> $rows @return Collection<int, object> */
    private function groupRows(Collection $rows, callable $group): Collection
    {
        return $rows->groupBy($group)->map(function (Collection $items, string $name): object {
            $views = (int) $items->sum('views');
            $clicks = (int) $items->sum('tool_clicks');

            return (object) [
                'name' => $name,
                'posts' => $items->count(),
                'views' => $views,
                'tool_clicks' => $clicks,
                'ctr' => $views > 0 ? round(($clicks / $views) * 100, 1) : 0.0,
                'subscriptions' => (int) $items->sum('subscriptions'),
            ];
        })->sortByDesc('views')->values();
    }

    /** @param Collection<int, object> $rows @return Collection<int, object> */
    private function tagRows(Collection $rows): Collection
    {
        return $rows->flatMap(fn (object $row): Collection => $row->tags->map(fn (string $tag): array => ['tag' => $tag, 'row' => $row]))
            ->groupBy('tag')->map(function (Collection $items, string $tag): object {
                $postRows = $items->pluck('row');
                return (object) ['name' => $tag, 'posts' => $postRows->count(), 'views' => (int) $postRows->sum('views'), 'subscriptions' => (int) $postRows->sum('subscriptions')];
            })->sortByDesc('views')->values();
    }

    /** @return Collection<int, object> */
    private function dailySeries(AnalyticsPeriod $period, ?int $postId = null): Collection
    {
        $rows = $this->events($period, $postId)
            ->selectRaw('DATE(occurred_at) as metric_date')
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_post_view' THEN 1 ELSE 0 END) as views")
            ->selectRaw("COUNT(DISTINCT CASE WHEN event_name = 'blog_post_view' THEN visitor_id END) as visitors")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_tool_click' THEN 1 ELSE 0 END) as tool_clicks")
            ->groupBy('metric_date')->get()->keyBy('metric_date');

        return collect(range(0, $period->days() - 1))->map(function (int $offset) use ($period, $rows): object {
            $date = $period->start->addDays($offset)->toDateString();
            $row = $rows->get($date);
            return (object) ['date' => $date, 'views' => (int) ($row?->views ?? 0), 'visitors' => (int) ($row?->visitors ?? 0), 'tool_clicks' => (int) ($row?->tool_clicks ?? 0)];
        });
    }

    /** @return Collection<int, object> */
    private function toolsForPost(AnalyticsPeriod $period, int $postId): Collection
    {
        $driver = DB::connection()->getDriverName();
        $tool = match ($driver) {
            'pgsql' => "metadata->>'tool_slug'",
            'sqlite' => "json_extract(metadata, '$.tool_slug')",
            default => "JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.tool_slug'))",
        };

        return $this->events($period, $postId)->where('event_name', 'blog_tool_click')
            ->selectRaw("$tool as tool_slug, COUNT(*) as clicks")
            ->groupByRaw($tool)->orderByDesc('clicks')->get();
    }

    private function events(AnalyticsPeriod $period, ?int $postId = null): Builder
    {
        return PlatformAnalyticsEvent::query()->where('channel', 'blog')
            ->whereBetween('occurred_at', [$period->start, $period->end])
            ->when($postId !== null, fn (Builder $query) => $query->where('subject_id', (string) $postId));
    }

    private function jsonNumber(string $key): string
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => "CAST(NULLIF(metadata->>'$key', '') AS NUMERIC)",
            'sqlite' => "CAST(json_extract(metadata, '$.$key') AS REAL)",
            default => "CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.$key')) AS DECIMAL(15,2))",
        };
    }
}
