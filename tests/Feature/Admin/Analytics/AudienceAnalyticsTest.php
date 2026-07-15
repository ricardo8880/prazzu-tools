<?php

namespace Tests\Feature\Admin\Analytics;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AudienceAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_audience_context_is_captured_and_persisted(): void
    {
        $response = $this->withHeaders([
            'X-Timezone' => 'America/Sao_Paulo',
            'X-Screen-Resolution' => '1920x1080',
            'X-Analytics-Language' => 'pt-BR',
        ])->postJson(route('analytics.audience.capture'), [
            'timezone' => 'America/Sao_Paulo',
            'screen_resolution' => '1920x1080',
            'language' => 'pt-BR',
        ]);

        $response->assertOk()->assertJson(['captured' => true]);
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => 'audience.context_captured',
            'timezone' => 'America/Sao_Paulo',
            'screen_resolution' => '1920x1080',
            'language' => 'pt-BR',
        ]);
    }

    public function test_audience_dashboard_displays_aggregated_data(): void
    {
        $visitorId = (string) Str::uuid();
        AnalyticsVisitor::query()->create([
            'id' => $visitorId,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
        AnalyticsSession::query()->create([
            'id' => (string) Str::uuid(),
            'visitor_id' => $visitorId,
            'started_at' => now(),
            'last_activity_at' => now(),
            'device_type' => 'mobile',
            'browser' => 'Chrome',
            'operating_system' => 'Android',
            'language' => 'pt-BR',
            'timezone' => 'America/Sao_Paulo',
            'screen_resolution' => '1080x1920',
            'country_code' => 'BR',
            'region' => 'SP',
            'city' => 'São Paulo',
        ]);

        $this->withoutMiddleware()->get(route('admin.analytics.audience', ['period' => '30']))
            ->assertOk()
            ->assertSee('Público')
            ->assertSee('Mobile')
            ->assertSee('São Paulo');
    }
}
