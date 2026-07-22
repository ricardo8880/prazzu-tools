<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Application\Queries\CampaignAnalyticsQuery;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampaignIntelligenceMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_roi_cost_per_acquisition_and_context_retention(): void
    {
        CarbonImmutable::setTestNow('2026-07-22 12:00:00');
        AnalyticsVisitor::query()->create([
            'id' => 'visitor-roi',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        $contextId = DB::table('acquisition_contexts')->insertGetId([
            'name' => 'Campanha MEI',
            'keyword' => 'mei-roi',
            'campaign_identifier' => 'campanha-mei',
            'status' => 'active',
            'monthly_investment_cents' => 30000,
            'investment_currency' => 'BRL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            ['session-initial', '2026-06-01 10:00:00'],
            ['session-return', '2026-06-05 10:00:00'],
        ] as [$id, $startedAt]) {
            AnalyticsSession::query()->create([
                'id' => $id,
                'visitor_id' => 'visitor-roi',
                'started_at' => $startedAt,
                'last_activity_at' => $startedAt,
                'acquisition_context_id' => $contextId,
                'acquisition_keyword' => 'mei-roi',
                'acquisition_campaign_identifier' => 'campanha-mei',
            ]);
        }

        PlatformAnalyticsEvent::query()->create([
            'event_id' => 'subscription-roi',
            'event_name' => 'subscription.created',
            'schema_version' => 1,
            'channel' => 'acquisition',
            'visitor_id' => 'visitor-roi',
            'analytics_session_id' => 'session-initial',
            'acquisition_context_id' => $contextId,
            'acquisition_keyword' => 'mei-roi',
            'acquisition_campaign_identifier' => 'campanha-mei',
            'metadata' => ['revenue_cents' => 60000],
            'occurred_at' => '2026-06-01 11:00:00',
        ]);

        $data = app(CampaignAnalyticsQuery::class)->execute(
            AnalyticsPeriod::between('2026-06-01', '2026-06-30'),
        );

        $roi = $data['roi']->first();
        self::assertSame(30000, $roi->cost_cents);
        self::assertSame(60000, $roi->revenue_cents);
        self::assertSame(30000, $roi->cost_per_subscription_cents);
        self::assertSame(2.0, $roi->roas);
        self::assertSame(100.0, $roi->roi);

        $retention = $data['retention']->first();
        self::assertSame('mei-roi', $retention->label);
        self::assertSame(1, $retention->retained_7d);
        self::assertSame(100.0, $retention->retention_7d);
    }
}
