<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

final class StrategicAnalyticsDecisionEngine
{
    /** @param array<string,mixed> $report @return array<string,mixed> */
    public function build(array $report): array
    {
        $summary = $report['summary'] ?? [];
        $funnel = $report['funnel'] ?? [];
        $tools = $report['tool_performance'] ?? [];
        $quality = $report['data_quality'] ?? [];
        $events = (int) ($summary['events']['value'] ?? 0);
        $visitors = (int) ($summary['visitors']['value'] ?? 0);
        $sessions = (int) ($summary['sessions']['value'] ?? 0);

        $sample = $this->sampleAssessment($events, $visitors, $sessions);
        $health = $this->healthScore($report, $sample);
        $opportunities = $this->opportunities($report, $sample);
        $alerts = $this->alerts($report, $sample);
        $decisions = $this->decisions($opportunities, $alerts, $sample);

        return [
            'sample_assessment' => $sample,
            'analytics_health' => $health,
            'benchmarks' => $this->benchmarks($summary, $funnel),
            'alerts' => $alerts,
            'opportunities' => $opportunities,
            'decisions' => $decisions,
            'action_plan' => $this->actionPlan($decisions),
            'projections' => $this->projections($summary, $report['daily_series'] ?? [], $sample),
            'limitations' => $this->limitations($sample, $quality),
        ];
    }

    /** @return array<string,mixed> */
    private function sampleAssessment(int $events, int $visitors, int $sessions): array
    {
        $level = match (true) {
            $visitors >= 500 && $sessions >= 500 => 'robust',
            $visitors >= 100 && $sessions >= 100 => 'usable',
            $visitors >= 20 && $sessions >= 20 => 'directional',
            default => 'insufficient',
        };

        return [
            'level' => $level,
            'events' => $events,
            'visitors' => $visitors,
            'sessions' => $sessions,
            'supports_decisions' => in_array($level, ['usable', 'robust'], true),
            'supports_directional_hypotheses' => $level !== 'insufficient',
            'message' => match ($level) {
                'robust' => 'Amostra robusta para análises segmentadas e decisões, mantendo validação causal.',
                'usable' => 'Amostra utilizável para decisões de baixo risco e experimentos controlados.',
                'directional' => 'Amostra direcional: use para formular hipóteses, não para decisões irreversíveis.',
                default => 'Amostra insuficiente: os números descrevem o recorte, mas não sustentam conclusões de negócio.',
            },
        ];
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $sample @return array<string,mixed> */
    private function healthScore(array $report, array $sample): array
    {
        $qualityFields = $report['data_quality']['fields'] ?? [];
        $coverages = collect($qualityFields)->pluck('coverage_rate')->filter(fn ($value): bool => is_numeric($value));
        $coverage = $coverages->isEmpty() ? 0.0 : round((float) $coverages->avg(), 1);
        $eventTypes = count($report['event_breakdown'] ?? []);
        $hasFunnel = ($report['funnel']['current']['opened'] ?? 0) > 0;
        $hasAcquisition = collect($report['channel_breakdown'] ?? [])->contains(fn (array $row): bool => ($row['name'] ?? '') !== '' && ($row['name'] ?? '') !== 'direct');

        $components = [
            'data_coverage' => ['score' => (int) round($coverage), 'weight' => 35, 'reason' => 'Média de preenchimento dos campos analíticos.'],
            'event_taxonomy' => ['score' => min(100, $eventTypes * 10), 'weight' => 20, 'reason' => 'Diversidade de eventos reconhecidos no período.'],
            'funnel_observability' => ['score' => $hasFunnel ? 100 : 0, 'weight' => 20, 'reason' => 'Disponibilidade das etapas do funil de ferramentas.'],
            'acquisition_observability' => ['score' => $hasAcquisition ? 100 : 40, 'weight' => 15, 'reason' => 'Presença de aquisição além de tráfego direto.'],
            'sample_reliability' => ['score' => match ($sample['level']) { 'robust' => 100, 'usable' => 75, 'directional' => 45, default => 10 }, 'weight' => 10, 'reason' => 'Tamanho da amostra para interpretação.'],
        ];

        $score = (int) round(collect($components)->sum(fn (array $item): float => $item['score'] * ($item['weight'] / 100)));

        return [
            'score' => $score,
            'status' => match (true) { $score >= 85 => 'excellent', $score >= 70 => 'good', $score >= 50 => 'attention', default => 'critical' },
            'components' => $components,
            'interpretation' => 'Este score mede observabilidade e confiabilidade do analytics, não desempenho comercial.',
        ];
    }

    /** @param array<string,mixed> $summary @param array<string,mixed> $funnel @return list<array<string,mixed>> */
    private function benchmarks(array $summary, array $funnel): array
    {
        $rows = [];
        foreach ($summary as $name => $metric) {
            if (! is_array($metric)) {
                continue;
            }
            $rows[] = [
                'metric' => (string) $name,
                'current' => $metric['value'] ?? 0,
                'previous' => $metric['previous'] ?? 0,
                'change_percent' => $metric['change'] ?? null,
                'benchmark_type' => 'previous_period',
                'status' => $this->changeStatus($metric['change'] ?? null),
            ];
        }
        foreach (($funnel['rates'] ?? []) as $name => $value) {
            $previous = $funnel['previous_rates'][$name] ?? null;
            $rows[] = [
                'metric' => (string) $name,
                'current' => $value,
                'previous' => $previous,
                'change_percentage_points' => $previous === null ? null : round((float) $value - (float) $previous, 1),
                'benchmark_type' => 'previous_period',
                'status' => $previous === null ? 'no_baseline' : $this->changeStatus((float) $value - (float) $previous),
            ];
        }

        return $rows;
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $sample @return list<array<string,mixed>> */
    private function opportunities(array $report, array $sample): array
    {
        $items = [];
        foreach ($report['tool_performance'] ?? [] as $tool) {
            $opened = (int) ($tool['opened'] ?? 0);
            $started = (int) ($tool['started'] ?? 0);
            $completed = (int) ($tool['completed'] ?? 0);
            if ($opened > 0 && $started === 0) {
                $items[] = $this->opportunity('tool_activation', 'Ativação da ferramenta '.$tool['name'], 'Há aberturas sem início de cálculo.', $opened, $sample, 'high', 'low', [
                    'Revisar CTA, proposta de valor e primeiro campo.',
                    'Testar o fluxo em mobile e desktop.',
                    'Instrumentar clique no CTA e erros antes do início.',
                ]);
            } elseif ($started > 0 && $completed === 0) {
                $items[] = $this->opportunity('tool_completion', 'Conclusão da ferramenta '.$tool['name'], 'Há cálculos iniciados sem conclusão.', $started, $sample, 'high', 'medium', [
                    'Revisar validações e mensagens de erro.',
                    'Identificar o último passo alcançado.',
                    'Comparar abandono por dispositivo.',
                ]);
            }
        }

        $channels = collect($report['channel_breakdown'] ?? []);
        $directVisitors = (int) ($channels->firstWhere('name', 'direct')['visitors'] ?? 0);
        $totalVisitors = max(1, (int) $channels->sum('visitors'));
        if ($directVisitors / $totalVisitors >= .8) {
            $items[] = $this->opportunity('acquisition_attribution', 'Diversificar e melhorar a atribuição de aquisição', 'Ao menos 80% dos visitantes atribuídos por canal estão em tráfego direto.', $totalVisitors, $sample, 'medium', 'medium', [
                'Padronizar UTMs em campanhas e links externos.',
                'Validar preservação de parâmetros até a conversão.',
                'Separar acessos internos e ambiente de teste.',
            ]);
        }

        return collect($items)->sortByDesc('priority_score')->take(10)->values()->all();
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $sample @return list<array<string,mixed>> */
    private function alerts(array $report, array $sample): array
    {
        $alerts = [];
        foreach ($report['summary'] ?? [] as $metric => $values) {
            $change = $values['change'] ?? null;
            if ($change !== null && abs((float) $change) >= 25) {
                $alerts[] = [
                    'severity' => (float) $change < 0 ? 'high' : 'info',
                    'type' => 'period_change',
                    'title' => ucfirst((string) $metric).' variou '.number_format((float) $change, 1, ',', '.').'%',
                    'evidence' => ['current' => $values['value'] ?? 0, 'previous' => $values['previous'] ?? 0, 'change_percent' => $change],
                    'confidence' => $sample['level'],
                    'requires_investigation' => true,
                ];
            }
        }
        foreach (($report['data_quality']['fields'] ?? []) as $field => $values) {
            if ((float) ($values['coverage_rate'] ?? 100) < 70) {
                $alerts[] = [
                    'severity' => 'medium', 'type' => 'data_quality',
                    'title' => 'Baixa cobertura do campo '.$field,
                    'evidence' => $values,
                    'confidence' => 'high', 'requires_investigation' => true,
                ];
            }
        }

        return array_slice($alerts, 0, 20);
    }

    /** @param list<array<string,mixed>> $opportunities @param list<array<string,mixed>> $alerts @param array<string,mixed> $sample @return list<array<string,mixed>> */
    private function decisions(array $opportunities, array $alerts, array $sample): array
    {
        $decisions = [];
        foreach ($opportunities as $index => $item) {
            $decisions[] = [
                'id' => 'decision-'.($index + 1),
                'priority' => $item['priority'],
                'title' => $item['title'],
                'decision' => $sample['supports_decisions'] ? 'Executar experimento controlado.' : 'Investigar antes de alterar permanentemente o produto.',
                'reason' => $item['observation'],
                'evidence' => $item['evidence'],
                'recommended_actions' => $item['actions'],
                'expected_impact' => 'Não estimado: o pacote não contém evidência suficiente para quantificar impacto causal.',
                'confidence' => $item['confidence'],
                'effort' => $item['effort'],
                'success_metric' => match ($item['type']) {
                    'tool_activation' => 'open_to_start',
                    'tool_completion' => 'start_to_complete',
                    default => 'identified_acquisition_rate',
                },
            ];
        }
        if ($decisions === [] && $alerts !== []) {
            $decisions[] = [
                'id' => 'decision-1', 'priority' => 'medium', 'title' => 'Investigar alertas do período',
                'decision' => 'Validar causas antes de qualquer realocação de investimento.',
                'reason' => 'Foram detectadas variações ou lacunas de dados relevantes.',
                'evidence' => ['alerts' => count($alerts)],
                'recommended_actions' => ['Revisar os alertas e segmentar por ferramenta, origem e dispositivo.'],
                'expected_impact' => 'Não estimado.', 'confidence' => $sample['level'], 'effort' => 'low', 'success_metric' => 'resolved_alerts',
            ];
        }

        return $decisions;
    }

    /** @param list<array<string,mixed>> $decisions @return array<string,list<array<string,mixed>>> */
    private function actionPlan(array $decisions): array
    {
        $plan = ['now' => [], 'this_week' => [], 'this_month' => [], 'later' => []];
        foreach ($decisions as $decision) {
            $bucket = match ([$decision['priority'], $decision['effort']]) {
                ['high', 'low'] => 'now',
                ['high', 'medium'], ['medium', 'low'] => 'this_week',
                ['medium', 'medium'], ['high', 'high'] => 'this_month',
                default => 'later',
            };
            $plan[$bucket][] = $decision;
        }

        return $plan;
    }

    /** @param array<string,mixed> $summary @param list<array<string,mixed>> $daily @param array<string,mixed> $sample @return array<string,mixed> */
    private function projections(array $summary, array $daily, array $sample): array
    {
        $days = max(1, count($daily));
        $metrics = [];
        foreach (['events', 'visitors', 'sessions', 'conversions'] as $name) {
            $value = (int) ($summary[$name]['value'] ?? 0);
            $metrics[$name] = ['observed' => $value, 'daily_run_rate' => round($value / $days, 2), 'projected_30_days' => (int) round(($value / $days) * 30)];
        }

        return [
            'method' => 'linear_run_rate',
            'eligible_for_planning' => $sample['supports_decisions'],
            'warning' => 'Projeção matemática baseada no ritmo observado; não é previsão causal e não inclui receita sem dados financeiros.',
            'metrics' => $metrics,
        ];
    }

    /** @param array<string,mixed> $sample @param array<string,mixed> $quality @return list<string> */
    private function limitations(array $sample, array $quality): array
    {
        $items = [$sample['message'], 'Benchmarks externos não são incluídos sem fonte ou configuração explícita.', 'Impacto financeiro não é estimado sem receita, ticket, custos e atribuição confiável.'];
        if (($quality['fields'] ?? []) === []) {
            $items[] = 'Não há métricas de cobertura de campos no recorte.';
        }

        return $items;
    }

    /** @param array<string,mixed> $sample @param list<string> $actions @return array<string,mixed> */
    private function opportunity(string $type, string $title, string $observation, int $sampleSize, array $sample, string $impact, string $effort, array $actions): array
    {
        $priorityScore = ($impact === 'high' ? 3 : 2) * ($effort === 'low' ? 3 : 2);

        return [
            'type' => $type, 'title' => $title, 'observation' => $observation,
            'priority' => $impact === 'high' ? 'high' : 'medium', 'priority_score' => $priorityScore,
            'impact' => $impact, 'effort' => $effort, 'confidence' => $sample['level'],
            'evidence' => ['sample_size' => $sampleSize], 'actions' => $actions,
        ];
    }

    private function changeStatus(mixed $change): string
    {
        if ($change === null) {
            return 'no_baseline';
        }

        return match (true) { (float) $change >= 10 => 'up', (float) $change <= -10 => 'down', default => 'stable' };
    }
}
