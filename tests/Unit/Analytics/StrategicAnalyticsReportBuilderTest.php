<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\StrategicAnalyticsReportBuilder;
use App\Core\Analytics\Domain\Catalog\AnalyticsEventCatalog;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class StrategicAnalyticsReportBuilderTest extends TestCase
{
    public function test_payload_is_self_describing_and_preserves_product_context(): void
    {
        CarbonImmutable::setTestNow('2026-07-19 12:00:00');
        $builder = new StrategicAnalyticsReportBuilder(new AnalyticsEventCatalog);
        $period = AnalyticsPeriod::between('2026-07-01', '2026-07-10');

        $payload = $builder->payload($period, ['tool' => 'simples-nacional'], $this->report());

        self::assertSame('1.1', $payload['report']['schema_version']);
        self::assertFalse($payload['product_context']['account_required_to_use_tools']);
        self::assertSame('tool.calculation.completed', $payload['product_context']['primary_value_event']);
        self::assertSame('Cálculo concluído', $payload['breakdowns']['events'][0]['label']);
        self::assertSame(['tool' => 'simples-nacional'], $payload['report']['filters']);
        self::assertNotEmpty($payload['ai_instructions']['rules']);
        self::assertSame(80.0, $payload['derived_metrics']['funnel']['rates']['start_to_complete']);
        self::assertNotEmpty($payload['strategic_insights']);
    }

    public function test_markdown_and_json_include_context_metrics_and_dictionary(): void
    {
        $builder = new StrategicAnalyticsReportBuilder(new AnalyticsEventCatalog);
        $payload = $builder->payload(AnalyticsPeriod::between('2026-07-01', '2026-07-10'), [], $this->report());

        $markdown = $builder->markdown($payload);
        $json = $builder->json($payload);

        self::assertStringContainsString('# Relatório Estratégico do Analytics', $markdown);
        self::assertStringContainsString('Cálculo concluído', $markdown);
        self::assertStringContainsString('Não trate correlação como causalidade', $markdown);
        self::assertStringContainsString('Insights estratégicos automáticos', $markdown);
        self::assertSame('1.1', json_decode($json, true, flags: JSON_THROW_ON_ERROR)['report']['schema_version']);
    }

    private function report(): array
    {
        return [
            'summary' => ['events' => ['value' => 10, 'previous' => 8, 'change' => 25.0]],
            'event_breakdown' => [['event_name' => 'tool.calculation.completed', 'total' => 5]],
            'tool_breakdown' => [['name' => 'simples-nacional', 'events' => 8, 'visitors' => 4, 'conversions' => 5]],
            'channel_breakdown' => [['name' => 'organic', 'events' => 7, 'visitors' => 4, 'conversions' => 4]],
            'source_breakdown' => [['name' => 'google', 'events' => 7, 'visitors' => 4, 'conversions' => 4]],
            'device_breakdown' => [['name' => 'mobile', 'events' => 7, 'visitors' => 4, 'conversions' => 4]],
            'funnel' => [
                'current' => ['opened' => 100, 'started' => 50, 'completed' => 40, 'exported' => 10],
                'previous' => ['opened' => 80, 'started' => 50, 'completed' => 45, 'exported' => 8],
                'rates' => ['open_to_start' => 50.0, 'start_to_complete' => 80.0, 'complete_to_export' => 25.0],
                'previous_rates' => ['open_to_start' => 62.5, 'start_to_complete' => 90.0, 'complete_to_export' => 17.8],
            ],
            'tool_performance' => [[
                'name' => 'simples-nacional', 'opened' => 100, 'started' => 25, 'completed' => 10, 'exported' => 2,
                'start_rate' => 25.0, 'completion_rate' => 40.0, 'export_rate' => 20.0,
                'previous' => ['start_rate' => 50.0, 'completion_rate' => 70.0],
                'start_rate_delta_pp' => -25.0, 'completion_rate_delta_pp' => -30.0,
            ]],
        ];
    }
}
