<?php

namespace Tests\Feature\Analytics;

use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AcquisitionEventCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contextual_home_exposes_the_campaign_collection_hooks(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Campanha Rescisão',
            'keyword' => 'rescisao-video',
            'campaign_identifier' => 'instagram-rescisao',
            'status' => 'active',
        ]);

        $this->followingRedirects()->get('/?context=rescisao-video')
            ->assertOk()
            ->assertSee('data-acquisition-impression="hero"', false)
            ->assertSee('data-acquisition-impression="cta"', false)
            ->assertSee(route('analytics.acquisition.track'), false)
            ->assertSee(AnalyticsEventName::AcquisitionContextResolved->value, false);
    }

    public function test_acquisition_event_uses_the_server_side_session_context(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Campanha Rescisão',
            'keyword' => 'rescisao-video',
            'campaign_identifier' => 'instagram-rescisao',
            'status' => 'active',
            'primary_tool_slug' => 'calculadora-rescisao',
        ]);

        $this->followingRedirects()->get('/?context=rescisao-video')->assertOk();

        $this->postJson(route('analytics.acquisition.track'), [
            'event' => AnalyticsEventName::AcquisitionToolClicked->value,
            'tool_slug' => 'calculadora-rescisao',
            'placement' => 'primary',
            'position' => 1,
            'destination' => '/ferramentas/calculadora-rescisao',
            'context_id' => 999999,
            'context_keyword' => 'valor-forjado',
        ])->assertOk()->assertJson(['recorded' => true]);

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::AcquisitionToolClicked->value)
            ->firstOrFail();

        self::assertSame(AnalyticsEventName::AcquisitionToolClicked->value, $event->event_name);
        self::assertSame('acquisition', $event->channel);
        self::assertSame($context->getKey(), $event->acquisition_context_id);
        self::assertSame('rescisao-video', $event->acquisition_keyword);
        self::assertSame('instagram-rescisao', $event->acquisition_campaign_identifier);
        self::assertSame('calculadora-rescisao', $event->metadata['tool_slug']);
        self::assertSame($context->getKey(), $event->metadata['context_id']);
        self::assertSame('rescisao-video', $event->metadata['context_keyword']);
    }

    public function test_acquisition_collection_rejects_requests_without_an_active_context(): void
    {
        $this->postJson(route('analytics.acquisition.track'), [
            'event' => AnalyticsEventName::AcquisitionHeroViewed->value,
        ])->assertUnprocessable();

        self::assertSame(0, PlatformAnalyticsEvent::query()->count());
    }

    public function test_tool_events_require_a_valid_tool_placement(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Campanha Rescisão',
            'keyword' => 'rescisao-video',
            'status' => 'active',
        ]);

        $this->followingRedirects()->get('/?context=rescisao-video')->assertOk();

        $this->postJson(route('analytics.acquisition.track'), [
            'event' => AnalyticsEventName::AcquisitionToolImpression->value,
            'tool_slug' => 'calculadora-rescisao',
            'placement' => 'invalido',
        ])->assertUnprocessable();
    }
}
