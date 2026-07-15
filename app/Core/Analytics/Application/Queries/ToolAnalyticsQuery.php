<?php

namespace App\Core\Analytics\Application\Queries;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Core\Tools\ToolCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final readonly class ToolAnalyticsQuery
{
    public function __construct(private ToolCatalog $catalog) {}

    /** @return array<string, mixed> */
    public function overview(AnalyticsPeriod $period): array
    {
        $metrics = $this->metrics($period)->keyBy('tool_slug');
        $tools = $this->catalog->all(false)->map(function (array $tool) use ($metrics): object {
            $row = $metrics->get($tool['slug']);
            return $this->row($tool, $row);
        })->sortByDesc('opens')->values();

        return [
            'period' => $period,
            'summary' => [
                'tools' => $tools->count(), 'opens' => (int) $tools->sum('opens'),
                'starts' => (int) $tools->sum('starts'), 'completions' => (int) $tools->sum('completions'),
                'exports' => (int) $tools->sum('exports'), 'shares' => (int) $tools->sum('shares'),
                'history' => (int) $tools->sum('history'), 'plus' => (int) $tools->sum('plus'),
            ],
            'tools' => $tools,
            'rankings' => [
                'most_opened' => $tools->sortByDesc('opens')->take(10)->values(),
                'most_completed' => $tools->sortByDesc('completions')->take(10)->values(),
                'highest_conversion' => $tools->filter(fn (object $r) => $r->starts > 0)->sortByDesc('conversion_rate')->take(10)->values(),
                'highest_abandonment' => $tools->filter(fn (object $r) => $r->starts > 0)->sortByDesc('abandonment_rate')->take(10)->values(),
                'most_exported' => $tools->sortByDesc('exports')->take(10)->values(),
            ],
            'daily' => $this->daily($period),
        ];
    }

    /** @return array<string, mixed> */
    public function tool(string $slug, AnalyticsPeriod $period): array
    {
        $tool = $this->catalog->find($slug);
        abort_if($tool === null, 404);
        $metric = $this->metrics($period, $slug)->first();

        return [
            'period' => $period,
            'tool' => $tool,
            'metrics' => $this->row($tool, $metric),
            'daily' => $this->daily($period, $slug),
            'devices' => $this->base($period, $slug)->selectRaw("COALESCE(device_type, 'unknown') as label, COUNT(*) as total")->groupBy('device_type')->orderByDesc('total')->get(),
            'sources' => $this->base($period, $slug)->selectRaw("COALESCE(source, 'direct') as label, COUNT(*) as total")->groupBy('source')->orderByDesc('total')->limit(10)->get(),
            'recent_events' => $this->base($period, $slug)->latest('occurred_at')->limit(30)->get(['event_name','device_type','source','occurred_at']),
        ];
    }

    private function row(array $tool, ?object $metric): object
    {
        $starts = (int) ($metric?->starts ?? 0); $completed = (int) ($metric?->completions ?? 0);
        $abandoned = max(0, $starts - $completed);
        return (object) array_merge($tool, [
            'opens' => (int) ($metric?->opens ?? 0), 'starts' => $starts, 'completions' => $completed,
            'exports' => (int) ($metric?->exports ?? 0), 'shares' => (int) ($metric?->shares ?? 0),
            'history' => (int) ($metric?->history ?? 0), 'registrations' => (int) ($metric?->registrations ?? 0),
            'plus' => (int) ($metric?->plus ?? 0), 'unique_visitors' => (int) ($metric?->unique_visitors ?? 0),
            'average_time_seconds' => (int) round((float) ($metric?->average_time_seconds ?? 0)),
            'abandonments' => $abandoned,
            'conversion_rate' => $starts > 0 ? round($completed / $starts * 100, 1) : 0.0,
            'abandonment_rate' => $starts > 0 ? round($abandoned / $starts * 100, 1) : 0.0,
        ]);
    }

    private function metrics(AnalyticsPeriod $period, ?string $slug = null): Collection
    {
        return $this->base($period, $slug)->whereNotNull('subject_slug')->selectRaw('subject_slug as tool_slug')
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.opened','tool.viewed') THEN 1 ELSE 0 END) as opens")
            ->selectRaw("SUM(CASE WHEN event_name = 'tool.calculation_started' THEN 1 ELSE 0 END) as starts")
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.calculation_completed','business_document_validator.batch_processed') THEN 1 ELSE 0 END) as completions")
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.exported','business_document_validator.batch_exported') THEN 1 ELSE 0 END) as exports")
            ->selectRaw("SUM(CASE WHEN event_name = 'tool.shared' THEN 1 ELSE 0 END) as shares")
            ->selectRaw("SUM(CASE WHEN event_name = 'tool.history_viewed' THEN 1 ELSE 0 END) as history")
            ->selectRaw("SUM(CASE WHEN event_name IN ('account.created','user.registered') THEN 1 ELSE 0 END) as registrations")
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.plus_used','subscription.started','subscription.created','plus.subscribed') THEN 1 ELSE 0 END) as plus")
            ->selectRaw("COUNT(DISTINCT visitor_id) as unique_visitors")
            ->selectRaw("AVG(CASE WHEN event_name = 'tool.time_spent' THEN ".$this->jsonNumber('seconds')." END) as average_time_seconds")
            ->groupBy('subject_slug')->get();
    }

    private function daily(AnalyticsPeriod $period, ?string $slug = null): Collection
    {
        return $this->base($period, $slug)->selectRaw($this->dateExpression().' as day')
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.opened','tool.viewed') THEN 1 ELSE 0 END) as opens")
            ->selectRaw("SUM(CASE WHEN event_name = 'tool.calculation_started' THEN 1 ELSE 0 END) as starts")
            ->selectRaw("SUM(CASE WHEN event_name IN ('tool.calculation_completed','business_document_validator.batch_processed') THEN 1 ELSE 0 END) as completions")
            ->groupBy('day')->orderBy('day')->get();
    }

    private function base(AnalyticsPeriod $period, ?string $slug = null): Builder
    {
        return PlatformAnalyticsEvent::query()->where('channel', 'tool')->whereBetween('occurred_at', [$period->start, $period->end])
            ->when($slug, fn (Builder $q) => $q->where('subject_slug', $slug));
    }

    private function dateExpression(): string { return match (PlatformAnalyticsEvent::query()->getConnection()->getDriverName()) { 'pgsql' => 'DATE(occurred_at)', default => 'DATE(occurred_at)' }; }
    private function jsonNumber(string $key): string { return match (PlatformAnalyticsEvent::query()->getConnection()->getDriverName()) { 'pgsql' => "CAST(metadata->>'$key' AS DECIMAL(12,2))", 'sqlite' => "CAST(json_extract(metadata, '$.$key') AS REAL)", default => "CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.$key')) AS DECIMAL(12,2))" }; }
}
