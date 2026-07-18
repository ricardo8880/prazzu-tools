<?php

namespace App\Core\Analytics\Application\Queries;

use App\Blog\Models\BlogPost;
use App\Core\Analytics\Application\Services\BlogTechnicalSeoAuditor;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\SeoMetricSnapshot;
use Illuminate\Support\Collection;

final readonly class SeoAnalyticsQuery
{
    public function __construct(private BlogTechnicalSeoAuditor $auditor) {}

    /** @return array<string,mixed> */
    public function execute(AnalyticsPeriod $period): array
    {
        $posts = BlogPost::query()->with('author')->latest('updated_at')->get();
        $metrics = $this->metrics($period);
        $rows = $posts->map(function (BlogPost $post) use ($metrics): object {
            $audit = $this->auditor->audit($post);
            $metric = $metrics->get($post->getKey(), $this->emptyMetric());

            return (object) array_merge([
                'post' => $post,
                'score' => $audit['score'],
                'audit_status' => $audit['status'],
                'warnings' => $audit['warnings'],
                'errors' => $audit['errors'],
                'word_count' => $audit['word_count'],
                'images_without_alt' => $audit['images']['without_alt'],
                'internal_links' => $audit['links']['internal'],
                'external_links' => $audit['links']['external'],
            ], (array) $metric);
        });

        return [
            'period' => $period,
            'summary' => (object) [
                'posts' => $rows->count(),
                'indexable' => $posts->where('should_index', true)->count(),
                'average_score' => round((float) $rows->avg('score'), 1),
                'critical' => $rows->where('errors', '>', 0)->count(),
                'clicks' => (int) $rows->sum('clicks'),
                'impressions' => (int) $rows->sum('impressions'),
                'ctr' => $this->rate((int) $rows->sum('clicks'), (int) $rows->sum('impressions')),
                'average_position' => $this->weightedPosition($rows),
                'discover_clicks' => (int) $rows->sum('discover_clicks'),
                'news_clicks' => (int) $rows->sum('news_clicks'),
                'rich_result_clicks' => (int) $rows->sum('rich_result_clicks'),
            ],
            'posts' => $rows->sortByDesc('clicks')->values(),
            'rankings' => [
                'best_ctr' => $rows->where('impressions', '>', 0)->sortByDesc('ctr')->take(8)->values(),
                'best_position' => $rows->whereNotNull('average_position')->sortBy('average_position')->take(8)->values(),
                'most_impressions' => $rows->sortByDesc('impressions')->take(8)->values(),
                'needs_attention' => $rows->sortBy('score')->take(8)->values(),
            ],
        ];
    }

    /** @return array<string,mixed> */
    public function forPost(BlogPost $post, AnalyticsPeriod $period): array
    {
        $audit = $this->auditor->audit($post);
        $base = SeoMetricSnapshot::query()->where('blog_post_id', $post->getKey())
            ->whereDate('metric_date', '>=', $period->start->toDateString())
            ->whereDate('metric_date', '<=', $period->end->toDateString());
        $totals = (clone $base)->selectRaw('SUM(clicks) clicks, SUM(impressions) impressions, SUM(discover_clicks) discover_clicks, SUM(discover_impressions) discover_impressions, SUM(news_clicks) news_clicks, SUM(news_impressions) news_impressions, SUM(rich_result_clicks) rich_result_clicks, SUM(rich_result_impressions) rich_result_impressions, SUM(average_position * impressions) weighted_position, SUM(CASE WHEN average_position IS NOT NULL THEN impressions ELSE 0 END) positioned_impressions')->first();
        $clicks = (int) ($totals->clicks ?? 0);
        $impressions = (int) ($totals->impressions ?? 0);
        $positioned = (int) ($totals->positioned_impressions ?? 0);

        return [
            'post' => $post,
            'period' => $period,
            'audit' => $audit,
            'metrics' => (object) [
                'clicks' => $clicks,
                'impressions' => $impressions,
                'ctr' => $this->rate($clicks, $impressions),
                'average_position' => $positioned > 0 ? round((float) $totals->weighted_position / $positioned, 2) : null,
                'discover_clicks' => (int) ($totals->discover_clicks ?? 0),
                'discover_impressions' => (int) ($totals->discover_impressions ?? 0),
                'news_clicks' => (int) ($totals->news_clicks ?? 0),
                'news_impressions' => (int) ($totals->news_impressions ?? 0),
                'rich_result_clicks' => (int) ($totals->rich_result_clicks ?? 0),
                'rich_result_impressions' => (int) ($totals->rich_result_impressions ?? 0),
            ],
            'daily' => (clone $base)->selectRaw('metric_date, SUM(clicks) clicks, SUM(impressions) impressions')->groupBy('metric_date')->orderBy('metric_date')->get(),
            'devices' => (clone $base)->selectRaw('device, SUM(clicks) clicks, SUM(impressions) impressions')->groupBy('device')->orderByDesc('clicks')->get(),
            'snapshots' => (clone $base)->latest('metric_date')->latest('id')->limit(30)->get(),
        ];
    }

    private function metrics(AnalyticsPeriod $period): Collection
    {
        return SeoMetricSnapshot::query()
            ->whereDate('metric_date', '>=', $period->start->toDateString())
            ->whereDate('metric_date', '<=', $period->end->toDateString())
            ->selectRaw('blog_post_id, SUM(clicks) clicks, SUM(impressions) impressions, SUM(discover_clicks) discover_clicks, SUM(news_clicks) news_clicks, SUM(rich_result_clicks) rich_result_clicks, SUM(average_position * impressions) weighted_position, SUM(CASE WHEN average_position IS NOT NULL THEN impressions ELSE 0 END) positioned_impressions')
            ->groupBy('blog_post_id')->get()->mapWithKeys(function ($row): array {
                $clicks = (int) $row->clicks;
                $impressions = (int) $row->impressions;
                $positioned = (int) $row->positioned_impressions;

                return [$row->blog_post_id => (object) [
                    'clicks' => $clicks, 'impressions' => $impressions,
                    'ctr' => $this->rate($clicks, $impressions),
                    'average_position' => $positioned > 0 ? round((float) $row->weighted_position / $positioned, 2) : null,
                    'discover_clicks' => (int) $row->discover_clicks,
                    'news_clicks' => (int) $row->news_clicks,
                    'rich_result_clicks' => (int) $row->rich_result_clicks,
                ]];
            });
    }

    private function emptyMetric(): object
    {
        return (object) ['clicks' => 0, 'impressions' => 0, 'ctr' => 0.0, 'average_position' => null, 'discover_clicks' => 0, 'news_clicks' => 0, 'rich_result_clicks' => 0];
    }

    private function rate(int $part, int $total): float
    {
        return $total > 0 ? round($part / $total * 100, 2) : 0.0;
    }

    private function weightedPosition(Collection $rows): ?float
    {
        $weighted = $rows->sum(fn ($row) => ($row->average_position ?? 0) * $row->impressions);
        $impressions = $rows->whereNotNull('average_position')->sum('impressions');

        return $impressions > 0 ? round($weighted / $impressions, 2) : null;
    }
}
