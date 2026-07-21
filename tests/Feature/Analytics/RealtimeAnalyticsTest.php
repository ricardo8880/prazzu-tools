<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsToolPresence;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Analytics\Concerns\ActsAsInternalAdministrator;
use Tests\TestCase;

final class RealtimeAnalyticsTest extends TestCase
{
    use ActsAsInternalAdministrator, RefreshDatabase;

    public function test_realtime_dashboard_and_json_endpoint_show_current_activity(): void
    {
        $this->signInAsInternalAdministrator();
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
        AnalyticsToolPresence::query()->create([
            'id' => (string) Str::uuid(), 'tool_slug' => 'simples-nacional',
            'visitor_id' => $visitorId, 'analytics_session_id' => $sessionId,
            'path' => '/ferramentas/simples-nacional', 'source' => 'google',
            'last_seen_at' => now(),
        ]);

        $this->get(route('admin.analytics.realtime'))
            ->assertOk()->assertSee('Tempo real')->assertSee('simples-nacional');

        $this->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('summary.online_users', 1)
            ->assertJsonPath('summary.open_tools', 1)
            ->assertJsonPath('tools.0.label', 'simples-nacional');
    }

    public function test_tool_presence_appears_on_heartbeat_and_disappears_on_leave(): void
    {
        $presenceId = (string) Str::uuid();

        $this->postJson(route('analytics.tools.presence'), [
            'presence_id' => $presenceId,
            'tool' => 'calculadora-simples-nacional',
            'action' => 'heartbeat',
        ])->assertNoContent();

        $this->assertDatabaseHas('analytics_tool_presences', [
            'id' => $presenceId,
            'tool_slug' => 'calculadora-simples-nacional',
        ]);

        $this->signInAsInternalAdministrator();
        $this->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()
            ->assertJsonPath('summary.open_tools', 1)
            ->assertJsonPath('tools.0.label', 'calculadora-simples-nacional')
            ->assertJsonPath('tools.0.total', 1);

        $this->postJson(route('analytics.tools.presence'), [
            'presence_id' => $presenceId,
            'tool' => 'calculadora-simples-nacional',
            'action' => 'leave',
        ])->assertNoContent();

        $this->assertDatabaseMissing('analytics_tool_presences', ['id' => $presenceId]);
        $this->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()
            ->assertJsonPath('summary.open_tools', 0)
            ->assertJsonCount(0, 'tools');
    }

    public function test_stale_tool_presence_is_not_counted(): void
    {
        AnalyticsToolPresence::query()->create([
            'id' => (string) Str::uuid(),
            'tool_slug' => 'simples-nacional',
            'last_seen_at' => now()->subSeconds(30),
        ]);

        $this->signInAsInternalAdministrator();
        $this->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()
            ->assertJsonPath('summary.open_tools', 0)
            ->assertJsonCount(0, 'tools');
    }

    public function test_inactive_sessions_are_not_counted_as_online(): void
    {
        $this->signInAsInternalAdministrator();
        $visitorId = (string) Str::uuid();
        AnalyticsVisitor::query()->create([
            'id' => $visitorId,
            'first_seen_at' => now()->subHour(),
            'last_seen_at' => now()->subMinutes(10),
        ]);
        AnalyticsSession::query()->create([
            'id' => (string) Str::uuid(), 'visitor_id' => $visitorId, 'started_at' => now()->subHour(),
            'last_activity_at' => now()->subMinutes(10), 'landing_path' => '/',
        ]);

        $this->getJson(route('admin.analytics.realtime.data'))
            ->assertOk()->assertJsonPath('summary.online_users', 0);
    }
}
