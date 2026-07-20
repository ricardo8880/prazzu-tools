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
        ];

        if (! $summaryOnly) {
            $files += [
                'eventos.csv' => $this->rawFiles->build('csv', $rows, 'Eventos do Analytics'),
                'ferramentas.csv' => $this->breakdownCsv($payload['derived_metrics']['tool_performance'] ?? [], 'tool'),
                'canais.csv' => $this->breakdownCsv($payload['breakdowns']['channels'] ?? [], 'dimension'),
                'origens.csv' => $this->breakdownCsv($payload['breakdowns']['sources'] ?? [], 'dimension'),
                'dispositivos.csv' => $this->breakdownCsv($payload['breakdowns']['devices'] ?? [], 'dimension'),
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
        ]);
        if (! $summaryOnly) {
            $lines = array_merge($lines, [
                '- `eventos.csv`: eventos brutos do recorte, limitado pela configuração de exportação.',
                '- `ferramentas.csv`: funil e taxas por ferramenta.',
                '- `canais.csv`, `origens.csv` e `dispositivos.csv`: detalhamentos de aquisição e uso.',
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
