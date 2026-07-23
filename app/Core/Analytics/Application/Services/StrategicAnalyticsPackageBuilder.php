<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use Illuminate\Support\Collection;

final class StrategicAnalyticsPackageBuilder
{
    public function __construct(
        private readonly StrategicAnalyticsReportBuilder $reports,
        private readonly AnalyticsReportFileBuilder $rawFiles,
        private readonly SimpleZipArchiveBuilder $zip,
    ) {}

    /** @param array<string,mixed> $payload @param Collection<int,object> $rows */
    public function build(array $payload, Collection $rows, bool $summaryOnly = false): string
    {
        $files = [
            'LEIA-ME.md' => $this->readme($payload, $summaryOnly),
            'resumo-estrategico.md' => $this->reports->markdown($payload),
            'metricas.json' => $this->reports->json($payload),
            'dicionario-de-dados.md' => $this->dictionary($payload),
            'insights.csv' => $this->insightsCsv($payload['strategic_insights'] ?? []),
            'decisoes-priorizadas.csv' => $this->decisionsCsv($payload['decision_support']['decisions'] ?? []),
            'alertas.csv' => $this->alertsCsv($payload['decision_support']['alerts'] ?? []),
            'benchmarks.csv' => $this->benchmarksCsv($payload['decision_support']['benchmarks'] ?? []),
            'plano-de-acao.md' => $this->actionPlanMarkdown($payload['decision_support'] ?? []),
        ];

        if (! $summaryOnly) {
            $files += [
                'eventos.csv' => $this->rawFiles->build('csv', $rows, 'Eventos do Analytics'),
                'ferramentas.csv' => $this->breakdownCsv($payload['derived_metrics']['tool_performance'] ?? [], 'tool'),
                'eventos-por-tipo.csv' => $this->eventsCsv($payload['breakdowns']['events'] ?? []),
                'canais.csv' => $this->breakdownCsv($payload['breakdowns']['channels'] ?? [], 'dimension'),
                'origens.csv' => $this->breakdownCsv($payload['breakdowns']['sources'] ?? [], 'dimension'),
                'midias.csv' => $this->breakdownCsv($payload['breakdowns']['mediums'] ?? [], 'dimension'),
                'campanhas.csv' => $this->breakdownCsv($payload['breakdowns']['campaigns'] ?? [], 'dimension'),
                'paginas.csv' => $this->breakdownCsv($payload['breakdowns']['pages'] ?? [], 'dimension'),
                'referenciadores.csv' => $this->breakdownCsv($payload['breakdowns']['referrers'] ?? [], 'dimension'),
                'tipos-de-objeto.csv' => $this->breakdownCsv($payload['breakdowns']['subject_types'] ?? [], 'dimension'),
                'contextos-de-aquisicao.csv' => $this->breakdownCsv($payload['breakdowns']['acquisition_contexts'] ?? [], 'dimension'),
                'dispositivos.csv' => $this->breakdownCsv($payload['breakdowns']['devices'] ?? [], 'dimension'),
                'navegadores.csv' => $this->breakdownCsv($payload['breakdowns']['browsers'] ?? [], 'dimension'),
                'sistemas-operacionais.csv' => $this->breakdownCsv($payload['breakdowns']['operating_systems'] ?? [], 'dimension'),
                'idiomas.csv' => $this->breakdownCsv($payload['breakdowns']['languages'] ?? [], 'dimension'),
                'paises.csv' => $this->breakdownCsv($payload['breakdowns']['countries'] ?? [], 'dimension'),
                'estados-regioes.csv' => $this->breakdownCsv($payload['breakdowns']['regions'] ?? [], 'dimension'),
                'cidades.csv' => $this->breakdownCsv($payload['breakdowns']['cities'] ?? [], 'dimension'),
                'serie-diaria.csv' => $this->timeSeriesCsv($payload['time_series']['daily'] ?? []),
                'serie-horaria.csv' => $this->timeSeriesCsv($payload['time_series']['hourly'] ?? []),
                'qualidade-dos-dados.csv' => $this->dataQualityCsv($payload['data_quality'] ?? []),
            ];
        }

        return $this->zip->build($files);
    }

    /** @param array<string,mixed> $payload */
    private function readme(array $payload, bool $summaryOnly): string
    {
        $period = $payload['report']['period'];
        $filters = $payload['report']['filters'] ?: ['nenhum' => 'Todos os dados do período'];
        $lines = [
            '# Pacote Estratégico do Prazzu Tools', '',
            'Este pacote foi preparado para análise humana e por inteligência artificial.', '',
            '## Escopo', '',
            '- Tipo: '.($summaryOnly ? 'resumido' : 'completo'),
            '- Período: '.$period['label'],
            '- Schema: '.$payload['report']['schema_version'],
            '- Gerado em: '.$payload['report']['generated_at'], '',
            '## Contexto essencial', '',
            '- As ferramentas podem ser usadas sem conta.',
            '- Conclusão de cálculo representa entrega de valor.',
            '- Criação de conta representa continuidade, não requisito de uso.',
            '- Correlação entre métricas não demonstra causalidade.', '',
            '## Filtros aplicados', '',
        ];
        foreach ($filters as $key => $value) {
            $lines[] = '- '.$key.': '.(is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE));
        }
        $lines = array_merge($lines, ['', '## Como usar no ChatGPT', '',
            'Envie este ZIP e peça para analisar todos os arquivos em conjunto. Solicite que fatos, inferências e hipóteses sejam separados e que toda recomendação cite as métricas de suporte.', '',
            'Prompt recomendado:', '',
            '> Analise este pacote como consultor de produto, growth e estratégia. Identifique tendências, gargalos, anomalias e oportunidades. Separe fatos, inferências e hipóteses. Não trate correlação como causalidade. Priorize recomendações por impacto, confiança e esforço e cite as métricas que sustentam cada ação.', '',
            '## Arquivos', '',
            '- `resumo-estrategico.md`: leitura executiva completa.',
            '- `metricas.json`: estrutura canônica e versionada.',
            '- `dicionario-de-dados.md`: definições dos eventos e métricas.',
            '- `insights.csv`: observações, hipóteses e ações em formato tabular.',
            '- `decisoes-priorizadas.csv`: decisões sugeridas com evidências, confiança, esforço e métrica de sucesso.',
            '- `alertas.csv`: variações e lacunas que exigem investigação.',
            '- `benchmarks.csv`: comparação interna com o período anterior.',
            '- `plano-de-acao.md`: sequência de ações por horizonte de execução.',
        ]);
        if (! $summaryOnly) {
            $lines = array_merge($lines, [
                '- `eventos.csv`: eventos brutos do recorte, limitado pela configuração de exportação.',
                '- `ferramentas.csv`: funil e taxas por ferramenta.',
                '- CSVs especializados: eventos, ferramentas, canais, origens, mídias, campanhas, páginas, referenciadores, tecnologia, geografia, séries temporais e qualidade dos dados.',
            ]);
        }

        return implode("\n", $lines)."\n";
    }

    /** @param array<string,mixed> $payload */
    private function dictionary(array $payload): string
    {
        $lines = ['# Dicionário de Dados', '', '## Métricas', ''];
        foreach ($payload['data_dictionary']['metrics'] ?? [] as $name => $description) {
            $lines[] = '- `'.$name.'`: '.$description;
        }
        $lines[] = '';
        $lines[] = '## Eventos';
        $lines[] = '';
        foreach ($payload['data_dictionary']['events'] ?? [] as $event) {
            $lines[] = '### '.$event['label'].' (`'.$event['event_name'].'`)';
            $lines[] = '';
            $lines[] = '- Categoria: '.$event['category'];
            $lines[] = '- Descrição: '.$event['description'];
            $lines[] = '- Significado de negócio: '.$event['business_meaning'];
            $lines[] = '';
        }

        return implode("\n", $lines)."\n";
    }

    /** @param list<array<string,mixed>> $insights */
    private function insightsCsv(array $insights): string
    {
        $rows = [['Prioridade', 'Tipo', 'Título', 'Observação', 'Confiança', 'Evidências JSON', 'Hipóteses', 'Ações']];
        foreach ($insights as $insight) {
            $rows[] = [
                $insight['priority'] ?? '', $insight['type'] ?? '', $insight['title'] ?? '', $insight['observation'] ?? '',
                $insight['confidence'] ?? '', json_encode($insight['evidence'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                implode(' | ', $insight['hypotheses'] ?? []), implode(' | ', $insight['actions'] ?? []),
            ];
        }

        return $this->csv($rows);
    }


    /** @param list<array<string,mixed>> $decisions */
    private function decisionsCsv(array $decisions): string
    {
        $rows = [['ID', 'Prioridade', 'Título', 'Decisão', 'Motivo', 'Confiança', 'Esforço', 'Métrica de sucesso', 'Impacto esperado', 'Evidências JSON', 'Ações']];
        foreach ($decisions as $decision) {
            $rows[] = [
                $decision['id'] ?? '', $decision['priority'] ?? '', $decision['title'] ?? '', $decision['decision'] ?? '',
                $decision['reason'] ?? '', $decision['confidence'] ?? '', $decision['effort'] ?? '', $decision['success_metric'] ?? '',
                $decision['expected_impact'] ?? '', json_encode($decision['evidence'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                implode(' | ', $decision['recommended_actions'] ?? []),
            ];
        }

        return $this->csv($rows);
    }

    /** @param list<array<string,mixed>> $alerts */
    private function alertsCsv(array $alerts): string
    {
        $rows = [['Severidade', 'Tipo', 'Título', 'Confiança', 'Exige investigação', 'Evidências JSON']];
        foreach ($alerts as $alert) {
            $rows[] = [
                $alert['severity'] ?? '', $alert['type'] ?? '', $alert['title'] ?? '', $alert['confidence'] ?? '',
                ($alert['requires_investigation'] ?? false) ? 'sim' : 'não',
                json_encode($alert['evidence'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
        }

        return $this->csv($rows);
    }

    /** @param list<array<string,mixed>> $benchmarks */
    private function benchmarksCsv(array $benchmarks): string
    {
        $rows = [['Métrica', 'Atual', 'Anterior', 'Variação (%)', 'Variação (p.p.)', 'Tipo de benchmark', 'Status']];
        foreach ($benchmarks as $benchmark) {
            $rows[] = [
                $benchmark['metric'] ?? '', $benchmark['current'] ?? '', $benchmark['previous'] ?? '',
                $benchmark['change_percent'] ?? '', $benchmark['change_percentage_points'] ?? '',
                $benchmark['benchmark_type'] ?? '', $benchmark['status'] ?? '',
            ];
        }

        return $this->csv($rows);
    }

    /** @param array<string,mixed> $support */
    private function actionPlanMarkdown(array $support): string
    {
        $sample = $support['sample_assessment'] ?? [];
        $health = $support['analytics_health'] ?? [];
        $labels = ['now' => 'Agora', 'this_week' => 'Esta semana', 'this_month' => 'Este mês', 'later' => 'Depois'];
        $lines = [
            '# Plano de Ação do Analytics', '',
            '- Nível da amostra: **'.strtoupper((string) ($sample['level'] ?? 'unknown')).'**',
            '- Sustenta decisões: **'.(($sample['supports_decisions'] ?? false) ? 'sim' : 'não').'**',
            '- Analytics Health Score: **'.($health['score'] ?? 0).'/100**', '',
            '> Ações permanentes devem ser precedidas por investigação ou experimento quando a amostra não sustenta decisões.', '',
        ];
        foreach ($labels as $key => $label) {
            $lines[] = '## '.$label;
            $lines[] = '';
            $items = $support['action_plan'][$key] ?? [];
            if ($items === []) {
                $lines[] = '- Nenhuma ação automática neste horizonte.';
            }
            foreach ($items as $decision) {
                $lines[] = '- [ ] **'.$decision['title'].'** — '.$decision['decision'].' Métrica: `'.$decision['success_metric'].'`.';
            }
            $lines[] = '';
        }
        $lines[] = '## Limitações';
        $lines[] = '';
        foreach ($support['limitations'] ?? [] as $limitation) {
            $lines[] = '- '.$limitation;
        }

        return implode("
", $lines)."
";
    }

    /** @param list<array<string,mixed>> $rows */
    private function eventsCsv(array $rows): string
    {
        $output = [['Evento', 'Rótulo', 'Categoria', 'Quantidade', 'Descrição', 'Significado de negócio']];
        foreach ($rows as $row) {
            $output[] = [$row['event_name'] ?? '', $row['label'] ?? '', $row['category'] ?? '', $row['total'] ?? 0, $row['description'] ?? '', $row['business_meaning'] ?? ''];
        }

        return $this->csv($output);
    }

    /** @param list<array<string,mixed>> $rows */
    private function timeSeriesCsv(array $rows): string
    {
        $output = [['Período', 'Eventos', 'Visitantes', 'Sessões']];
        foreach ($rows as $row) {
            $output[] = [$row['bucket'] ?? '', $row['events'] ?? 0, $row['visitors'] ?? 0, $row['sessions'] ?? 0];
        }

        return $this->csv($output);
    }

    /** @param array<string,mixed> $quality */
    private function dataQualityCsv(array $quality): string
    {
        $output = [['Campo', 'Preenchidos', 'Ausentes', 'Cobertura (%)']];
        foreach ($quality['fields'] ?? [] as $field => $metrics) {
            $output[] = [$field, $metrics['present'] ?? 0, $metrics['missing'] ?? 0, $metrics['coverage_rate'] ?? 0];
        }
        $output[] = ['eventos_identificados', $quality['identified_events'] ?? 0, '', ''];
        $output[] = ['eventos_anonimos', $quality['anonymous_events'] ?? 0, '', ''];

        return $this->csv($output);
    }

    /** @param list<array<string,mixed>> $rows */
    private function breakdownCsv(array $rows, string $type): string
    {
        if ($type === 'tool') {
            $output = [['Ferramenta', 'Aberturas', 'Inícios', 'Conclusões', 'Exportações', 'Taxa de início (%)', 'Taxa de conclusão (%)', 'Taxa de exportação (%)', 'Variação início (pp)', 'Variação conclusão (pp)']];
            foreach ($rows as $row) {
                $output[] = [$row['name'] ?? '', $row['opened'] ?? 0, $row['started'] ?? 0, $row['completed'] ?? 0, $row['exported'] ?? 0, $row['start_rate'] ?? 0, $row['completion_rate'] ?? 0, $row['export_rate'] ?? 0, $row['start_rate_delta_pp'] ?? '', $row['completion_rate_delta_pp'] ?? ''];
            }

            return $this->csv($output);
        }

        $output = [['Nome', 'Eventos', 'Visitantes', 'Conversões']];
        foreach ($rows as $row) {
            $output[] = [$row['name'] ?? 'Não informado', $row['events'] ?? 0, $row['visitors'] ?? 0, $row['conversions'] ?? 0];
        }

        return $this->csv($output);
    }

    /** @param list<list<mixed>> $rows */
    private function csv(array $rows): string
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($stream, $row, ';');
        }
        rewind($stream);
        $content = stream_get_contents($stream) ?: '';
        fclose($stream);

        return $content;
    }
}
