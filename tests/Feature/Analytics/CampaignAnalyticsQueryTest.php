<?php

namespace Tests\Feature\Analytics;

use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Analytics\Application\Queries\CampaignAnalyticsQuery;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CampaignAnalyticsQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_campaign_keyword_context_tool_and_funnel_metrics(): void
    {
        $now = CarbonImmutable::now();
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Campanha DAS MEI',
            'keyword' => 'das mei',
            'campaign_identifier' => 'video-mei',
            'status' => 'active',
            'primary_tool_slug' => 'calculadora-das',
        ]);

        AnalyticsVisitor::query()->create([
            'id' => 'visitor-campaign-1',
            'first_seen_at' => $now,
            'last_seen_at' => $now,
        ]);
        AnalyticsSession::query()->create([
            'id' => 'session-campaign-1', 'visitor_id' => 'visitor-campaign-1', 'started_at' => $now,
            'last_activity_at' => $now, 'acquisition_context_id' => $context->getKey(),
            'acquisition_keyword' => 'das mei', 'acquisition_campaign_identifier' => 'video-mei',
            'acquisition_primary_tool_slug' => 'calculadora-das', 'source' => 'instagram',
        ]);

        foreach ([
            ['acquisition.cta.viewed', ['cta_identifier' => 'hero-primary']],
            ['acquisition.cta.clicked', ['cta_identifier' => 'hero-primary']],
            ['acquisition.tool.clicked', ['tool_slug' => 'calculadora-das']],
            ['acquisition.tool.clicked', ['tool_slug' => 'simulador-mei']],
            ['tool.calculation.started', []],
            ['tool.calculation.completed', []],
            ['account.created', []],
        ] as $index => [$name, $metadata]) {
            PlatformAnalyticsEvent::query()->create([
                'event_id' => 'event-'.$index, 'event_name' => $name, 'schema_version' => 1,
                'channel' => 'acquisition', 'visitor_id' => 'visitor-campaign-1', 'analytics_session_id' => 'session-campaign-1',
                'acquisition_context_id' => $context->getKey(), 'acquisition_keyword' => 'das mei',
                'acquisition_campaign_identifier' => 'video-mei', 'source' => 'instagram', 'metadata' => $metadata,
                'occurred_at' => $now,
            ]);
        }

        $data = app(CampaignAnalyticsQuery::class)->execute(new AnalyticsPeriod($now->subDay(), $now->addDay()));

        self::assertSame(1, $data['summary']['sessions']);
        self::assertSame('video-mei', $data['campaigns']->first()->label);
        self::assertSame('das mei', $data['keywords']->first()->label);
        self::assertSame('calculadora-das', $data['tools']->first()->tool);
        self::assertSame(1, $data['campaigns']->first()->calculations_completed);
        self::assertSame(1, $data['campaigns']->first()->accounts);
        self::assertSame('Clique em ferramenta', $data['funnels']->first()['steps'][1]['label']);
        self::assertSame('instagram', $data['origins']->first()->label);
        self::assertSame('hero-primary', $data['ctas']->first()->label);
        self::assertSame('calculadora-das → simulador-mei', $data['journeys']->first()->journey);
    }
}
