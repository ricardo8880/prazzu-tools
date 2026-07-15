<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_open_is_captured_by_the_central_middleware(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index'))->assertOk();
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => 'tool.opened', 'channel' => 'tool',
            'subject_slug' => 'calculadora-margem-markup',
        ]);
    }

    public function test_browser_can_publish_a_normalized_tool_event(): void
    {
        $this->postJson(route('analytics.tools.track'), [
            'tool' => 'calculadora-margem-markup', 'event' => 'tool.calculation_started',
        ])->assertNoContent();
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => 'tool.calculation_started', 'subject_slug' => 'calculadora-margem-markup',
        ]);
    }

    public function test_administrator_can_open_tools_dashboard(): void
    {
        PlatformAnalyticsEvent::query()->create([
            'event_id' => fake()->uuid(), 'event_name' => 'tool.opened', 'schema_version' => 1,
            'channel' => 'tool', 'subject_type' => 'tool', 'subject_slug' => 'calculadora-margem-markup',
            'metadata' => [], 'occurred_at' => now(),
        ]);
        $this->get(route('admin.analytics.tools'))->assertOk()->assertSee('Analytics das Ferramentas');
    }
}
