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

    public function test_continuity_entry_is_measured_without_capturing_calculation_payloads(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index', [
            'source' => 'home_recent_session',
        ]))->assertOk();

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::RetentionContinuityUsed->value)
            ->firstOrFail();

        self::assertSame('calculadora-margem-markup', $event->subject_slug);
        self::assertSame('home_recent_session', $event->metadata['placement']);
        self::assertArrayNotHasKey('input_payload', $event->metadata);
        self::assertArrayNotHasKey('result_payload', $event->metadata);
    }

    public function test_related_tool_entry_records_only_valid_catalog_attribution(): void
    {
        $this->get(route('tools.calculadora-ferias.index', [
            'source' => 'related_tools',
            'from_tool' => 'calculadora-salario-liquido',
            'position' => 2,
        ]))->assertOk();

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::RetentionRelatedToolOpened->value)
            ->firstOrFail();

        self::assertSame('calculadora-ferias', $event->subject_slug);
        self::assertSame('calculadora-salario-liquido', $event->metadata['from_tool']);
        self::assertSame(2, $event->metadata['position']);

        $this->get(route('tools.calculadora-ferias.index', [
            'source' => 'related_tools',
            'from_tool' => 'slug-inexistente',
        ]))->assertOk();

        self::assertSame(1, PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::RetentionRelatedToolOpened->value)
            ->count());
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

    public function test_browser_can_publish_a_journey_event_without_persisting_sensitive_values(): void
    {
        $this->postJson(route('analytics.tools.track'), [
            'tool' => 'calculadora-margem-markup',
            'event' => AnalyticsEventName::ToolFieldCompleted->value,
            'schema_version' => 1,
            'metadata' => [
                'journey_id' => 'journey-1',
                'form' => 'main',
                'step' => 'input',
                'field' => 'monthly_revenue',
                'completion_percentage' => 25,
                'cpf' => '12345678900',
                'value' => '1000,00',
            ],
        ])->assertNoContent();

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::ToolFieldCompleted->value)
            ->firstOrFail();

        self::assertSame('main', $event->metadata['form']);
        self::assertSame('monthly_revenue', $event->metadata['field']);
        self::assertArrayNotHasKey('cpf', $event->metadata);
        self::assertArrayNotHasKey('value', $event->metadata);
    }

    public function test_browser_rejects_unknown_tool_journey_events(): void
    {
        $this->postJson(route('analytics.tools.track'), [
            'tool' => 'calculadora-margem-markup',
            'event' => 'tool.payload.captured',
        ])->assertUnprocessable();
    }

    public function test_repeated_tool_events_in_the_same_session_are_deduplicated(): void
    {
        $payload = [
            'tool' => 'calculadora-margem-markup',
            'event' => AnalyticsEventName::ToolCalculationStarted->value,
        ];

        $this->postJson(route('analytics.tools.track'), $payload)->assertNoContent();
        $this->postJson(route('analytics.tools.track'), $payload)->assertNoContent();

        self::assertSame(1, PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::ToolCalculationStarted->value)
            ->where('subject_slug', 'calculadora-margem-markup')
            ->count());
    }

    public function test_prefetch_requests_do_not_create_page_views_or_tool_opens(): void
    {
        $this->withHeader('Purpose', 'prefetch')
            ->get(route('tools.calculadora-margem-markup.index'))
            ->assertOk();

        self::assertSame(0, PlatformAnalyticsEvent::query()->count());
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

    public function test_product_dashboard_uses_declared_journey_events_and_period_comparison(): void
    {
        $this->signInAsInternalAdministrator();
        $slug = 'calculadora-margem-markup';
        $journey = fake()->uuid();

        foreach ([
            [AnalyticsEventName::ToolOpened, now()->subMinutes(4), []],
            [AnalyticsEventName::ToolStarted, now()->subMinutes(3), ['journey_id' => $journey]],
            [AnalyticsEventName::ToolCalculationExecuted, now()->subMinutes(2), ['journey_id' => $journey]],
            [AnalyticsEventName::ToolResultViewed, now()->subMinute(), ['journey_id' => $journey]],
            [AnalyticsEventName::ToolValidationError, now(), ['journey_id' => $journey, 'field' => 'purchase_price', 'step' => 'input']],
        ] as [$event, $occurredAt, $metadata]) {
            PlatformAnalyticsEvent::query()->create([
                'event_id' => fake()->uuid(), 'event_name' => $event->value, 'schema_version' => 1,
                'channel' => 'tool', 'subject_type' => 'tool', 'subject_slug' => $slug,
                'metadata' => $metadata, 'occurred_at' => $occurredAt,
            ]);
        }

        $this->get(route('admin.analytics.tools', ['period' => '7', 'tool' => $slug]))
            ->assertOk()
            ->assertSee('100,0%')
            ->assertSee('purchase_price')
            ->assertSee('60,0s');
    }
}
