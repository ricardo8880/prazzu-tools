<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class RealtimeAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_realtime_dashboard_and_json_endpoint_show_current_activity(): void
    {
        $visitorId = (string) Str::uuid();
        $sessionId = (string) Str::uuid();
        AnalyticsVisitor::query()->create(['id' => $visitorId, 'first_seen_at' => now(), 'last_seen_at' => now()]);
        AnalyticsSession::query()->create([
            'id' => $sessionId, 'visitor_id' => $visitorId, 'started_at' => now()->subMinute(),
            'last_activity_at' => now(), 'landing_path' => '/blog/rescisao', 'source' => 'google',
            'country_code' => 'BR', 'region' => 'SP', 'city' => 'São Paulo',
        ]);
        PlatformAnalyticsEvent::query()->create([
            'event_id' => (string) Str::uuid(), 'event_name' => 'tool.opened', 'schema_version' => 1,
            'channel' => 'tool', 'subject_slug' => 'simples-nacional', 'visitor_id' => $visitorId,
            'analytics_session_id' => $sessionId, 'path' => '/ferramentas/simples-nacional',
            'source' => 'google', 'device_type' => 'mobile', 'occurred_at' => now(), 'metadata' => [],
        ]);

        $this->withoutMiddleware()->get(route('admin.analytics.realtime'))
            ->assertOk()->assertSee('Tempo real')->assertSee('simples-nacional');

        $this->withoutMiddleware()->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('summary.online_users', 1)
            ->assertJsonPath('summary.open_tools', 1)
            ->assertJsonPath('tools.0.label', 'simples-nacional');
    }

    public function test_inactive_sessions_are_not_counted_as_online(): void
    {
        AnalyticsSession::query()->create([
            'id' => (string) Str::uuid(), 'started_at' => now()->subHour(),
            'last_activity_at' => now()->subMinutes(10), 'landing_path' => '/',
        ]);

        $this->withoutMiddleware()->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()->assertJsonPath('summary.online_users', 0);
    }
}
