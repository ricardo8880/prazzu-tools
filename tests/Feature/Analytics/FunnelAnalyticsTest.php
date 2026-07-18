<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\AnalyticsFunnel;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Analytics\Concerns\ActsAsInternalAdministrator;
use Tests\TestCase;

final class FunnelAnalyticsTest extends TestCase
{
    use ActsAsInternalAdministrator, RefreshDatabase;

    public function test_it_calculates_a_standard_funnel_in_event_order(): void
    {
        $this->signInAsInternalAdministrator();
        $visitor = AnalyticsVisitor::query()->create(['id' => fake()->uuid(), 'first_seen_at' => now(), 'last_seen_at' => now()]);

        foreach (['page.viewed', 'blog.reading.started', 'tool.opened', 'tool.calculation.completed', 'account.created', 'subscription.started'] as $index => $event) {
            PlatformAnalyticsEvent::query()->create([
                'event_id' => fake()->uuid(), 'event_name' => $event, 'schema_version' => 1,
                'channel' => 'platform', 'visitor_id' => $visitor->id, 'metadata' => [],
                'occurred_at' => now()->subMinutes(10 - $index),
            ]);
        }

        $response = $this->get(route('admin.analytics.funnels', ['funnel' => 'standard:full_journey']));

        $response->assertOk()->assertSee('Jornada completa até o Plus')->assertSee('100,0%');
    }

    public function test_it_creates_and_deletes_a_custom_funnel(): void
    {
        $this->signInAsInternalAdministrator();

        $response = $this->post(route('admin.analytics.funnels.store'), [
            'name' => 'Cadastro', 'identity_type' => 'visitor',
            'steps' => "Visitou|page.viewed\nCadastrou|account.created",
        ]);

        $response->assertRedirect();
        $funnel = AnalyticsFunnel::query()->with('steps')->firstOrFail();
        $this->assertCount(2, $funnel->steps);

        $this->delete(route('admin.analytics.funnels.destroy', $funnel))->assertRedirect();
        $this->assertDatabaseMissing('analytics_funnels', ['id' => $funnel->id]);
    }
}
