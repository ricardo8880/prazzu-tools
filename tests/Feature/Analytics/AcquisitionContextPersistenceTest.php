<?php

namespace Tests\Feature\Analytics;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

final class AcquisitionContextPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_context_is_persisted_in_the_analytics_session_and_propagated_to_later_events(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Rescisão Instagram',
            'keyword' => 'rescisao-video-01',
            'campaign_identifier' => 'instagram-rescisao',
            'status' => AcquisitionContextStatus::Active,
            'primary_tool_slug' => 'calculadora-rescisao',
        ]);

        $sessionStore = app('session')->driver();
        $sessionStore->start();

        $landingRequest = Request::create('/?context=rescisao-video-01', 'GET');
        $landingRequest->setLaravelSession($sessionStore);

        app(PlatformAnalytics::class)->track(
            AnalyticsEvent::make(AnalyticsEventName::PageViewed->value, 'platform'),
            $landingRequest,
        );

        $navigationRequest = Request::create('/ferramentas/calculadora-rescisao', 'GET');
        $navigationRequest->setLaravelSession($sessionStore);

        app(PlatformAnalytics::class)->track(
            AnalyticsEvent::make(AnalyticsEventName::ToolCalculationStarted->value, 'tool'),
            $navigationRequest,
        );

        $analyticsSession = AnalyticsSession::query()->firstOrFail();
        self::assertSame($context->getKey(), $analyticsSession->acquisition_context_id);
        self::assertSame('rescisao-video-01', $analyticsSession->acquisition_keyword);
        self::assertSame('instagram-rescisao', $analyticsSession->acquisition_campaign_identifier);
        self::assertSame('calculadora-rescisao', $analyticsSession->acquisition_primary_tool_slug);

        $events = PlatformAnalyticsEvent::query()->orderBy('id')->get();
        self::assertCount(2, $events);

        foreach ($events as $event) {
            self::assertSame($context->getKey(), $event->acquisition_context_id);
            self::assertSame('rescisao-video-01', $event->acquisition_keyword);
            self::assertSame('instagram-rescisao', $event->acquisition_campaign_identifier);
            self::assertSame('calculadora-rescisao', $event->acquisition_primary_tool_slug);
        }
    }
}
