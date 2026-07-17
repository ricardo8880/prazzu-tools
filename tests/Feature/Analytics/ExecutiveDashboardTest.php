<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ExecutiveDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_displays_executive_metrics_and_comparison(): void
    {
        CarbonImmutable::setTestNow('2026-07-15 12:00:00');

        $visitorA = $this->visitor('2026-07-15 08:00:00');
        $visitorB = $this->visitor('2026-07-15 09:00:00');
        $sessionA = $this->analyticsSession($visitorA, '2026-07-15 08:00:00', '2026-07-15 08:05:00', 'google');
        $sessionB = $this->analyticsSession($visitorB, '2026-07-15 09:00:00', '2026-07-15 09:01:00', 'direct');

        $this->event($visitorA, $sessionA, 'page.viewed', '2026-07-15 08:00:00', '/', 'google');
        $this->event($visitorA, $sessionA, 'page.viewed', '2026-07-15 08:02:00', '/sobre', 'google');
        $this->event($visitorA, $sessionA, 'account.created', '2026-07-15 08:04:00', '/criar-conta', 'google');
        $this->event($visitorA, $sessionA, 'subscription.started', '2026-07-15 08:05:00', '/planos', 'google', ['revenue_cents' => 4990]);
        $this->event($visitorB, $sessionB, 'page.viewed', '2026-07-15 09:00:00', '/', 'direct');

        $response = $this->get(route('admin.analytics.index', ['period' => 'today']));

        $response->assertOk()
            ->assertSee('Dashboard executivo')
            ->assertSee('Visitantes únicos')
            ->assertSee('Bounce rate')
            ->assertSee('R$ 49,90')
            ->assertSee('Principais origens')
            ->assertSee('Páginas mais visitadas');
    }

    public function test_dashboard_accepts_a_custom_period(): void
    {
        CarbonImmutable::setTestNow('2026-07-15 12:00:00');

        $this->get(route('admin.analytics.index', [
            'period' => 'custom',
            'start' => '2026-07-01',
            'end' => '2026-07-15',
        ]))->assertOk()->assertSee('01/07/2026 a 15/07/2026');
    }

    public function test_dashboard_rejects_an_invalid_custom_period(): void
    {
        $this->get(route('admin.analytics.index', [
            'period' => 'custom',
            'start' => '2026-07-15',
            'end' => '2026-07-01',
        ]))->assertSessionHasErrors('end');
    }

    private function visitor(string $seenAt): string
    {
        $id = (string) Str::uuid();

        AnalyticsVisitor::query()->create([
            'id' => $id,
            'first_seen_at' => $seenAt,
            'last_seen_at' => $seenAt,
        ]);

        return $id;
    }

    private function analyticsSession(string $visitorId, string $startedAt, string $lastActivityAt, string $source): string
    {
        $id = (string) Str::uuid();

        AnalyticsSession::query()->create([
            'id' => $id,
            'visitor_id' => $visitorId,
            'started_at' => $startedAt,
            'last_activity_at' => $lastActivityAt,
            'source' => $source,
        ]);

        return $id;
    }

    /** @param array<string, mixed> $metadata */
    private function event(
        string $visitorId,
        string $sessionId,
        string $name,
        string $occurredAt,
        string $path,
        string $source,
        array $metadata = [],
    ): void {
        PlatformAnalyticsEvent::query()->create([
            'event_id' => (string) Str::uuid(),
            'event_name' => $name,
            'schema_version' => 1,
            'channel' => 'platform',
            'visitor_id' => $visitorId,
            'analytics_session_id' => $sessionId,
            'path' => $path,
            'source' => $source,
            'metadata' => $metadata,
            'occurred_at' => $occurredAt,
        ]);
    }
}
