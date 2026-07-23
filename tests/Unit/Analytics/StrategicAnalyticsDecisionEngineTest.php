<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\StrategicAnalyticsDecisionEngine;
use PHPUnit\Framework\TestCase;

final class StrategicAnalyticsDecisionEngineTest extends TestCase
{
    public function test_small_samples_generate_investigation_instead_of_confident_execution(): void
    {
        $support = (new StrategicAnalyticsDecisionEngine)->build([
            'summary' => [
                'events' => ['value' => 29, 'previous' => 0, 'change' => null],
                'visitors' => ['value' => 1, 'previous' => 0, 'change' => null],
                'sessions' => ['value' => 1, 'previous' => 0, 'change' => null],
                'conversions' => ['value' => 0, 'previous' => 0, 'change' => null],
            ],
            'tool_performance' => [[
                'name' => 'calculadora-ferias', 'opened' => 2, 'started' => 0,
                'completed' => 0, 'exported' => 0,
            ]],
            'channel_breakdown' => [['name' => 'direct', 'events' => 29, 'visitors' => 1, 'conversions' => 0]],
            'daily_series' => [['bucket' => '2026-07-23', 'events' => 29, 'visitors' => 1, 'sessions' => 1]],
            'data_quality' => ['fields' => ['visitor_id' => ['coverage_rate' => 100]]],
        ]);

        self::assertSame('insufficient', $support['sample_assessment']['level']);
        self::assertFalse($support['sample_assessment']['supports_decisions']);
        self::assertStringContainsString('Investigar', $support['decisions'][0]['decision']);
        self::assertStringContainsString('Não estimado', $support['decisions'][0]['expected_impact']);
        self::assertFalse($support['projections']['eligible_for_planning']);
    }

    public function test_health_score_is_explicitly_about_observability(): void
    {
        $support = (new StrategicAnalyticsDecisionEngine)->build([
            'summary' => [
                'events' => ['value' => 1000, 'previous' => 900, 'change' => 11.1],
                'visitors' => ['value' => 600, 'previous' => 500, 'change' => 20.0],
                'sessions' => ['value' => 650, 'previous' => 550, 'change' => 18.2],
            ],
            'event_breakdown' => array_fill(0, 10, ['event_name' => 'page.viewed']),
            'funnel' => ['current' => ['opened' => 100]],
            'channel_breakdown' => [['name' => 'organic', 'visitors' => 600]],
            'data_quality' => ['fields' => ['visitor_id' => ['coverage_rate' => 100], 'source' => ['coverage_rate' => 90]]],
        ]);

        self::assertSame('robust', $support['sample_assessment']['level']);
        self::assertGreaterThanOrEqual(85, $support['analytics_health']['score']);
        self::assertStringContainsString('não desempenho comercial', $support['analytics_health']['interpretation']);
    }
}
