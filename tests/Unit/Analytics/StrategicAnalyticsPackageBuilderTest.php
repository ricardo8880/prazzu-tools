<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\AnalyticsReportFileBuilder;
use App\Core\Analytics\Application\Services\SimpleZipArchiveBuilder;
use App\Core\Analytics\Application\Services\StrategicAnalyticsPackageBuilder;
use App\Core\Analytics\Application\Services\StrategicAnalyticsReportBuilder;
use App\Core\Analytics\Domain\Catalog\AnalyticsEventCatalog;
use Illuminate\Support\Collection;
use Tests\TestCase;

final class StrategicAnalyticsPackageBuilderTest extends TestCase
{
    public function test_complete_package_contains_ai_context_and_specialized_files(): void
    {
        $builder = $this->builder();
        $zip = $builder->build($this->payload(), new Collection);

        foreach (['LEIA-ME.md', 'resumo-estrategico.md', 'metricas.json', 'dicionario-de-dados.md', 'insights.csv', 'decisoes-priorizadas.csv', 'alertas.csv', 'benchmarks.csv', 'plano-de-acao.md', 'eventos.csv', 'eventos-por-tipo.csv', 'ferramentas.csv', 'canais.csv', 'origens.csv', 'midias.csv', 'campanhas.csv', 'paginas.csv', 'referenciadores.csv', 'dispositivos.csv', 'navegadores.csv', 'sistemas-operacionais.csv', 'idiomas.csv', 'paises.csv', 'estados-regioes.csv', 'cidades.csv', 'serie-diaria.csv', 'serie-horaria.csv', 'qualidade-dos-dados.csv'] as $name) {
            self::assertStringContainsString($name, $zip);
        }
    }

    public function test_summary_package_omits_raw_and_specialized_csv_files(): void
    {
        $zip = $this->builder()->build($this->payload(), new Collection, true);

        self::assertStringContainsString('metricas.json', $zip);
        self::assertStringNotContainsString('eventos.csv', $zip);
        self::assertStringNotContainsString('ferramentas.csv', $zip);
    }

    private function builder(): StrategicAnalyticsPackageBuilder
    {
        $reports = new StrategicAnalyticsReportBuilder(new AnalyticsEventCatalog);

        return new StrategicAnalyticsPackageBuilder($reports, new AnalyticsReportFileBuilder, new SimpleZipArchiveBuilder);
    }

    private function payload(): array
    {
        return [
            'report' => ['schema_version' => '2.1', 'generated_at' => '2026-07-19T12:00:00-03:00', 'period' => ['label' => '01/07/2026 a 10/07/2026'], 'filters' => []],
            'product_context' => ['description' => 'Plataforma de ferramentas.'],
            'executive_summary' => [],
            'breakdowns' => ['events' => [], 'tools' => [], 'channels' => [], 'sources' => [], 'mediums' => [], 'campaigns' => [], 'pages' => [], 'referrers' => [], 'subject_types' => [], 'acquisition_contexts' => [], 'devices' => [], 'browsers' => [], 'operating_systems' => [], 'languages' => [], 'countries' => [], 'regions' => [], 'cities' => []],
            'time_series' => ['daily' => [], 'hourly' => []],
            'data_quality' => ['fields' => []],
            'derived_metrics' => ['funnel' => [], 'tool_performance' => []],
            'strategic_insights' => [],
            'decision_support' => [
                'sample_assessment' => ['level' => 'insufficient', 'supports_decisions' => false],
                'analytics_health' => ['score' => 10],
                'benchmarks' => [], 'alerts' => [], 'decisions' => [],
                'action_plan' => ['now' => [], 'this_week' => [], 'this_month' => [], 'later' => []],
                'limitations' => ['Amostra insuficiente.'],
            ],
            'data_dictionary' => ['events' => [], 'metrics' => []],
            'ai_instructions' => ['role' => 'Atue como consultor.', 'rules' => [], 'suggested_questions' => []],
        ];
    }
}
