<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Analytics\Concerns\ActsAsInternalAdministrator;
use Tests\TestCase;

final class ToolAnalyticsTest extends TestCase
{
    use ActsAsInternalAdministrator, RefreshDatabase;

    public function test_tool_open_is_captured_by_the_central_middleware(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index'))->assertOk();
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::ToolOpened->value, 'channel' => 'tool',
            'subject_slug' => 'calculadora-margem-markup',
        ]);
    }

    public function test_browser_can_publish_a_normalized_tool_event(): void
    {
        $this->postJson(route('analytics.tools.track'), [
            'tool' => 'calculadora-margem-markup', 'event' => AnalyticsEventName::ToolCalculationStarted->value,
        ])->assertNoContent();
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::ToolCalculationStarted->value, 'subject_slug' => 'calculadora-margem-markup',
        ]);
    }

    public function test_administrator_can_open_tools_dashboard(): void
    {
        $this->signInAsInternalAdministrator();
        PlatformAnalyticsEvent::query()->create([
            'event_id' => fake()->uuid(), 'event_name' => AnalyticsEventName::ToolOpened->value, 'schema_version' => 1,
            'channel' => 'tool', 'subject_type' => 'tool', 'subject_slug' => 'calculadora-margem-markup',
            'metadata' => [], 'occurred_at' => now(),
        ]);
        $this->get(route('admin.analytics.tools'))->assertOk()->assertSee('Analytics das Ferramentas');
    }
}
