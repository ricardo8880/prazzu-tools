<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\StrategicAnalyticsInsightGenerator;
use PHPUnit\Framework\TestCase;

final class StrategicAnalyticsInsightGeneratorTest extends TestCase
{
    public function test_it_separates_observation_evidence_hypotheses_and_actions(): void
    {
        $insights = (new StrategicAnalyticsInsightGenerator)->generate([
            'funnel' => [
                'current' => ['opened' => 200, 'started' => 60, 'completed' => 20, 'exported' => 4],
                'rates' => ['open_to_start' => 30.0, 'start_to_complete' => 33.3, 'complete_to_export' => 20.0],
                'previous_rates' => ['open_to_start' => 55.0, 'start_to_complete' => 70.0, 'complete_to_export' => 18.0],
            ],
            'tool_performance' => [],
            'channel_breakdown' => [],
        ]);

        self::assertNotEmpty($insights);
        self::assertSame('high', $insights[0]['priority']);
        self::assertArrayHasKey('evidence', $insights[0]);
        self::assertNotEmpty($insights[0]['hypotheses']);
        self::assertNotEmpty($insights[0]['actions']);
        self::assertContains($insights[0]['confidence'], ['medium', 'high']);
    }

    public function test_it_ignores_small_samples(): void
    {
        $insights = (new StrategicAnalyticsInsightGenerator)->generate([
            'funnel' => [
                'current' => ['opened' => 5, 'started' => 1, 'completed' => 0, 'exported' => 0],
                'rates' => ['open_to_start' => 20.0, 'start_to_complete' => 0.0, 'complete_to_export' => 0.0],
                'previous_rates' => ['open_to_start' => 80.0, 'start_to_complete' => 80.0, 'complete_to_export' => 0.0],
            ],
            'tool_performance' => [],
            'channel_breakdown' => [],
        ]);

        self::assertSame([], $insights);
    }
}
