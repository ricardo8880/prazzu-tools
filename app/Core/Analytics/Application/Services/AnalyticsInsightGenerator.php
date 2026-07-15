<?php

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsInsight;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class AnalyticsInsightGenerator
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}
    public function generate(AnalyticsPeriod $period): int
    {
        $previous = $period->previous();
        $items = collect()
            ->merge($this->traffic($period, $previous))
            ->merge($this->sources($period, $previous))
            ->merge($this->tools($period, $previous))
            ->merge($this->content($period));

        foreach ($items as $item) {
            $fingerprint = hash('sha256', implode('|', [$item['type'], $item['subject_type'] ?? '', $item['subject_slug'] ?? '', $item['metric_name'] ?? '', $period->start->toDateString(), $period->end->toDateString()]));
            $insight = AnalyticsInsight::query()->firstOrNew(['fingerprint' => $fingerprint]);
            $insight->fill($item + [
                'fingerprint' => $fingerprint, 'period_start' => $period->start->toDateString(),
                'period_end' => $period->end->toDateString(), 'generated_at' => now(),
            ]);
            if (! $insight->exists) {
                $insight->status = 'open';
            }
            $insight->save();
        }

        return $items->count();
    }

    private function traffic(AnalyticsPeriod $current, AnalyticsPeriod $previous): Collection
    {
        $events = $this->eventNames->expand(config('analytics.dashboard.page_view_events', [AnalyticsEventName::PageViewed->value]));
        $now = $this->base($current)->whereIn('event_name', $events)->count();
        $before = $this->base($previous)->whereIn('event_name', $events)->count();
        if ($before < (int) config('analytics.insights.minimum_baseline', 10)) return collect();
        $change = $this->change($now, $before);
        if (abs($change) < (float) config('analytics.insights.change_threshold_percent', 20)) return collect();
        $up = $change > 0;
        return collect([[
            'type' => $up ? 'trend' : 'alert', 'severity' => $up ? 'success' : 'danger',
            'title' => $up ? 'Crescimento relevante de tráfego' : 'Queda relevante de tráfego',
            'message' => sprintf('As visualizações %s %.1f%%, de %d para %d.', $up ? 'aumentaram' : 'caíram', abs($change), $before, $now),
            'recommendation' => $up ? 'Identifique as páginas e origens responsáveis pelo crescimento e amplifique o que funcionou.' : 'Revise aquisição, disponibilidade do site e conteúdos que perderam tráfego.',
            'metric_name'=>'page_views','current_value'=>$now,'previous_value'=>$before,'change_percent'=>$change,
        ]]);
    }

    private function sources(AnalyticsPeriod $current, AnalyticsPeriod $previous): Collection
    {
        $ai = ['chatgpt','gemini','claude','perplexity','copilot','grok','deepseek','mistral'];
        $now = $this->base($current)->whereIn('source', $ai)->count();
        $before = $this->base($previous)->whereIn('source', $ai)->count();
        if ($before < 3 || $now <= $before) return collect();
        $change = $this->change($now, $before);
        if ($change < 20) return collect();
        return collect([[
            'type'=>'opportunity','severity'=>'info','title'=>'Acessos vindos de IA estão crescendo',
            'message'=>sprintf('O tráfego de ferramentas de IA cresceu %.1f%%, de %d para %d eventos.', $change, $before, $now),
            'recommendation'=>'Fortaleça respostas diretas, dados estruturados e autoridade dos conteúdos mais citados por assistentes de IA.',
            'subject_type'=>'channel','subject_slug'=>'ai','metric_name'=>'ai_events','current_value'=>$now,'previous_value'=>$before,'change_percent'=>$change,
        ]]);
    }

    private function tools(AnalyticsPeriod $current, AnalyticsPeriod $previous): Collection
    {
        $currentRows = $this->toolRows($current)->keyBy('slug');
        $previousRows = $this->toolRows($previous)->keyBy('slug');
        return $currentRows->flatMap(function (object $row) use ($previousRows): array {
            $old = $previousRows->get($row->slug);
            if (!$old || $row->starts < 5 || $old->starts < 5) return [];
            $rate = $row->starts > 0 ? ($row->completions / $row->starts) * 100 : 0;
            $oldRate = $old->starts > 0 ? ($old->completions / $old->starts) * 100 : 0;
            $delta = round($rate - $oldRate, 1);
            if ($delta > -10) return [];
            return [[
                'type'=>'alert','severity'=>'warning','title'=>'Conversão de ferramenta em queda',
                'message'=>sprintf('A conversão da ferramenta %s caiu %.1f pontos percentuais.', $row->slug, abs($delta)),
                'recommendation'=>'Revise erros, experiência mobile, campos obrigatórios e clareza antes do resultado.',
                'subject_type'=>'tool','subject_slug'=>$row->slug,'metric_name'=>'conversion_rate','current_value'=>round($rate,1),'previous_value'=>round($oldRate,1),'change_percent'=>$delta,
            ]];
        })->values();
    }

    private function content(AnalyticsPeriod $period): Collection
    {
        $views = $this->base($period)->whereIn('event_name', $this->eventNames->expand([AnalyticsEventName::BlogPostViewed, AnalyticsEventName::PageViewed]))->where('channel','blog')->whereNotNull('subject_slug')
            ->selectRaw('subject_slug as slug, COUNT(*) as views')->groupBy('subject_slug')->get()->keyBy('slug');
        $clicks = $this->base($period)->whereIn('event_name', $this->eventNames->acceptedNamesFor(AnalyticsEventName::BlogToolClicked))->whereNotNull('subject_slug')
            ->selectRaw('subject_slug as slug, COUNT(*) as clicks')->groupBy('subject_slug')->pluck('clicks','slug');
        return $views->filter(fn(object $r) => $r->views >= 20 && ((int)($clicks[$r->slug] ?? 0) / max(1,$r->views) * 100) < 2)
            ->take(10)->map(fn(object $r) => [
                'type'=>'recommendation','severity'=>'warning','title'=>'Conteúdo com tráfego e baixa conversão',
                'message'=>sprintf('O artigo %s teve %d visualizações, mas poucos cliques em ferramentas.', $r->slug, $r->views),
                'recommendation'=>'Revise os CTAs, a relação entre conteúdo e ferramenta e o posicionamento das chamadas.',
                'subject_type'=>'blog_post','subject_slug'=>$r->slug,'metric_name'=>'tool_ctr','current_value'=>round(((int)($clicks[$r->slug] ?? 0)/max(1,$r->views))*100,1),'previous_value'=>null,'change_percent'=>null,
            ])->values();
    }

    private function toolRows(AnalyticsPeriod $period): Collection
    {
        $starts = $this->eventNames->acceptedNamesFor(AnalyticsEventName::ToolCalculationStarted);
        $completions = $this->eventNames->expand([
            AnalyticsEventName::ToolCalculationCompleted,
            AnalyticsEventName::BusinessDocumentValidatorBatchProcessed,
        ]);

        return $this->base($period)->where('channel','tool')->whereNotNull('subject_slug')->selectRaw('subject_slug as slug')
            ->selectRaw('SUM(CASE WHEN event_name IN ('.$this->placeholders($starts).') THEN 1 ELSE 0 END) as starts', $starts)
            ->selectRaw('SUM(CASE WHEN event_name IN ('.$this->placeholders($completions).') THEN 1 ELSE 0 END) as completions', $completions)
            ->groupBy('subject_slug')->get();
    }

    /** @param list<string> $values */
    private function placeholders(array $values): string
    {
        return implode(',', array_fill(0, max(1, count($values)), '?'));
    }

    private function base(AnalyticsPeriod $period): Builder { return PlatformAnalyticsEvent::query()->whereBetween('occurred_at', [$period->start,$period->end]); }
    private function change(float $now, float $before): float { return $before == 0.0 ? 100.0 : round((($now-$before)/$before)*100,1); }
}
