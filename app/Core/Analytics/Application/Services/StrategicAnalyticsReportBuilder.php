<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Domain\Catalog\AnalyticsEventCatalog;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;

final class StrategicAnalyticsReportBuilder
{
    public const SCHEMA_VERSION = '1.1';

    private readonly StrategicAnalyticsInsightGenerator $insightGenerator;

    public function __construct(private readonly AnalyticsEventCatalog $catalog, ?StrategicAnalyticsInsightGenerator $insightGenerator = null)
    {
        $this->insightGenerator = $insightGenerator ?? new StrategicAnalyticsInsightGenerator;
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $filters */
    public function payload(AnalyticsPeriod $period, array $filters, array $report): array
    {
        $events = collect($report['event_breakdown'] ?? [])->map(function (array $row): array {
            $definition = $this->catalog->describe((string) $row['event_name']);

            return $row + [
                'label' => $definition['label'],
                'category' => $definition['category'],
                'description' => $definition['description'],
                'business_meaning' => $definition['business_meaning'],
            ];
        })->values()->all();

        return [
            'report' => [
                'schema_version' => self::SCHEMA_VERSION,
                'type' => 'prazzu_tools_strategic_analytics',
                'generated_at' => now()->toIso8601String(),
                'period' => $this->period($period),
                'comparison_period' => $this->period($period->previous()),
                'filters' => $filters,
            ],
            'product_context' => [
                'product_name' => config('app.name', 'Prazzu Tools'),
                'description' => 'Plataforma de ferramentas pontuais para profissionais contábeis.',
                'account_required_to_use_tools' => false,
                'primary_value_event' => 'tool.calculation.completed',
                'commercial_conversion_events' => ['subscription.started', 'subscription.created'],
                'interpretation_notes' => [
                    'Uso sem conta é comportamento esperado e não representa abandono.',
                    'Conclusão de cálculo representa entrega de valor do produto.',
                    'Associação entre métricas não prova causalidade.',
                    'Amostras pequenas devem ser interpretadas com cautela.',
                ],
            ],
            'executive_summary' => $report['summary'] ?? [],
            'breakdowns' => [
                'events' => $events,
                'tools' => $report['tool_breakdown'] ?? [],
                'channels' => $report['channel_breakdown'] ?? [],
                'sources' => $report['source_breakdown'] ?? [],
                'devices' => $report['device_breakdown'] ?? [],
            ],
            'derived_metrics' => [
                'funnel' => $report['funnel'] ?? [],
                'tool_performance' => $report['tool_performance'] ?? [],
            ],
            'strategic_insights' => $this->insightGenerator->generate($report),
            'data_dictionary' => [
                'events' => collect($events)->map(fn (array $event) => collect($event)->only(['event_name', 'label', 'category', 'description', 'business_meaning'])->all())->all(),
                'metrics' => [
                    'events' => 'Quantidade total de eventos registrados.',
                    'visitors' => 'Visitantes distintos identificados por visitor_id.',
                    'sessions' => 'Sessões distintas identificadas pelo analytics.',
                    'users' => 'Usuários autenticados distintos.',
                    'conversions' => 'Eventos configurados como conversão no Core Analytics.',
                    'change' => 'Variação percentual em relação ao período imediatamente anterior de mesma duração.',
                    'start_rate' => 'Percentual de inícios em relação às aberturas da ferramenta.',
                    'completion_rate' => 'Percentual de conclusões em relação aos inícios.',
                    'export_rate' => 'Percentual de exportações em relação às conclusões.',
                    'delta_percentage_points' => 'Diferença absoluta entre duas taxas, expressa em pontos percentuais.',
                ],
            ],
            'ai_instructions' => [
                'role' => 'Atue como consultor de produto, growth e estratégia.',
                'rules' => [
                    'Separe fatos observados, inferências e hipóteses.',
                    'Não trate correlação como causalidade.',
                    'Cite as métricas que sustentam cada recomendação.',
                    'Sinalize limitações, baixa amostragem e dados ausentes.',
                    'Priorize ações por impacto esperado, confiança e esforço.',
                ],
                'suggested_questions' => [
                    'Quais são os principais gargalos e oportunidades do período?',
                    'Quais ferramentas e canais merecem maior investimento?',
                    'Que mudanças exigem investigação antes de uma decisão?',
                    'Monte um plano de ação priorizado por impacto e esforço.',
                ],
            ],
        ];
    }

    /** @param array<string,mixed> $payload */
    public function json(array $payload): string
    {
        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /** @param array<string,mixed> $payload */
    public function markdown(array $payload): string
    {
        $period = $payload['report']['period'];
        $comparison = $payload['report']['comparison_period'] ?? null;
        $lines = [
            '# Relatório Estratégico do Analytics', '',
            '## Contexto', '',
            $payload['product_context']['description'], '',
            '- Período: **'.$period['label'].'**',
            '- Comparação: **'.(is_array($comparison) ? ($comparison['label'] ?? 'Não informada') : 'Não informada').'**',
            '- Gerado em: `'.$payload['report']['generated_at'].'`',
            '- Versão do formato: `'.$payload['report']['schema_version'].'`', '',
            '> As ferramentas podem ser usadas sem conta. Conclusões de cálculo representam valor entregue; criação de conta é uma etapa de continuidade.', '',
            '## Resumo executivo', '',
            '| Métrica | Atual | Anterior | Variação |',
            '|---|---:|---:|---:|',
        ];
        foreach ($payload['executive_summary'] as $key => $metric) {
            $change = $metric['change'] === null ? 'Sem base' : number_format((float) $metric['change'], 1, ',', '.').'%';
            $lines[] = '| '.ucfirst((string) $key).' | '.number_format((float) $metric['value'], 0, ',', '.').' | '.number_format((float) $metric['previous'], 0, ',', '.').' | '.$change.' |';
        }
        $lines = array_merge($lines, ['', '## Eventos mais frequentes', '', '| Evento | Identificador | Categoria | Quantidade |', '|---|---|---|---:|']);
        foreach (array_slice($payload['breakdowns']['events'], 0, 25) as $row) {
            $lines[] = '| '.$row['label'].' | `'.$row['event_name'].'` | '.$row['category'].' | '.number_format((int) $row['total'], 0, ',', '.').' |';
        }
        foreach (['tools' => 'Ferramentas', 'channels' => 'Canais', 'sources' => 'Origens'] as $key => $title) {
            $lines = array_merge($lines, ['', '## '.$title, '', '| Identificador | Eventos | Visitantes | Conversões |', '|---|---:|---:|---:|']);
            foreach (array_slice($payload['breakdowns'][$key], 0, 25) as $row) {
                $lines[] = '| '.($row['name'] ?: 'Não informado').' | '.number_format((int) $row['events'], 0, ',', '.').' | '.number_format((int) $row['visitors'], 0, ',', '.').' | '.number_format((int) $row['conversions'], 0, ',', '.').' |';
            }
        }
        $funnel = $payload['derived_metrics']['funnel'] ?? [];
        if ($funnel !== []) {
            $lines = array_merge($lines, ['', '## Funil principal das ferramentas', '', '| Etapa | Quantidade | Taxa | Taxa anterior |', '|---|---:|---:|---:|']);
            $current = $funnel['current'] ?? [];
            $rates = $funnel['rates'] ?? [];
            $previousRates = $funnel['previous_rates'] ?? [];
            $lines[] = '| Ferramentas abertas | '.number_format((int) ($current['opened'] ?? 0), 0, ',', '.').' | — | — |';
            $lines[] = '| Cálculos iniciados | '.number_format((int) ($current['started'] ?? 0), 0, ',', '.').' | '.number_format((float) ($rates['open_to_start'] ?? 0), 1, ',', '.').'% | '.number_format((float) ($previousRates['open_to_start'] ?? 0), 1, ',', '.').'% |';
            $lines[] = '| Cálculos concluídos | '.number_format((int) ($current['completed'] ?? 0), 0, ',', '.').' | '.number_format((float) ($rates['start_to_complete'] ?? 0), 1, ',', '.').'% | '.number_format((float) ($previousRates['start_to_complete'] ?? 0), 1, ',', '.').'% |';
            $lines[] = '| Resultados exportados | '.number_format((int) ($current['exported'] ?? 0), 0, ',', '.').' | '.number_format((float) ($rates['complete_to_export'] ?? 0), 1, ',', '.').'% | '.number_format((float) ($previousRates['complete_to_export'] ?? 0), 1, ',', '.').'% |';
        }

        $lines = array_merge($lines, ['', '## Desempenho derivado por ferramenta', '', '| Ferramenta | Aberturas | Inícios | Conclusões | Taxa de início | Taxa de conclusão | Exportações |', '|---|---:|---:|---:|---:|---:|---:|']);
        foreach (array_slice($payload['derived_metrics']['tool_performance'] ?? [], 0, 25) as $row) {
            $lines[] = '| '.$row['name'].' | '.number_format((int) $row['opened'], 0, ',', '.').' | '.number_format((int) $row['started'], 0, ',', '.').' | '.number_format((int) $row['completed'], 0, ',', '.').' | '.number_format((float) $row['start_rate'], 1, ',', '.').'% | '.number_format((float) $row['completion_rate'], 1, ',', '.').'% | '.number_format((int) $row['exported'], 0, ',', '.').' |';
        }

        $lines = array_merge($lines, ['', '## Insights estratégicos automáticos', '']);
        if (($payload['strategic_insights'] ?? []) === []) {
            $lines[] = 'Nenhum alerta automático atingiu os critérios mínimos de amostra e relevância neste recorte.';
            $lines[] = '';
        }
        foreach ($payload['strategic_insights'] ?? [] as $insight) {
            $lines[] = '### ['.strtoupper($insight['priority']).'] '.$insight['title'];
            $lines[] = '';
            $lines[] = '**Observação:** '.$insight['observation'];
            $lines[] = '';
            $lines[] = '**Confiança:** '.$insight['confidence'];
            $lines[] = '';
            $lines[] = '**Hipóteses a verificar:**';
            foreach ($insight['hypotheses'] as $hypothesis) {
                $lines[] = '- '.$hypothesis;
            }
            $lines[] = '';
            $lines[] = '**Próximas verificações recomendadas:**';
            foreach ($insight['actions'] as $action) {
                $lines[] = '- '.$action;
            }
            $lines[] = '';
        }

        $lines = array_merge($lines, ['', '## Como analisar com IA', '', $payload['ai_instructions']['role'], '']);
        foreach ($payload['ai_instructions']['rules'] as $rule) {
            $lines[] = '- '.$rule;
        }
        $lines[] = '';
        $lines[] = '### Perguntas sugeridas';
        $lines[] = '';
        foreach ($payload['ai_instructions']['suggested_questions'] as $i => $question) {
            $lines[] = ($i + 1).'. '.$question;
        }
        $lines = array_merge($lines, ['', '## Dicionário de eventos', '']);
        foreach ($payload['data_dictionary']['events'] as $event) {
            $lines[] = '### '.$event['label'].' (`'.$event['event_name'].'`)';
            $lines[] = '';
            $lines[] = $event['description'].' '.$event['business_meaning'];
            $lines[] = '';
        }

        return implode("\n", $lines)."\n";
    }

    private function period(AnalyticsPeriod $period): array
    {
        return ['start' => $period->start->toDateString(), 'end' => $period->end->toDateString(), 'days' => $period->days(), 'label' => $period->label()];
    }
}
