<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AnalyticsCountingAccuracyTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_browser_visiting_three_pages_is_one_visitor_and_three_pageviews(): void
    {
        $this->get('/')->assertOk();
        $this->get('/planos')->assertOk();
        $this->get('/sobre')->assertOk();

        self::assertSame(1, AnalyticsVisitor::query()->count());
        self::assertSame(1, AnalyticsSession::query()->count());
        self::assertSame(3, PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::PageViewed->value)
            ->count());
    }

    public function test_revisiting_the_same_page_counts_as_another_real_pageview(): void
    {
        $this->get('/sobre')->assertOk();
        $this->get('/sobre')->assertOk();

        self::assertSame(1, AnalyticsVisitor::query()->count());
        self::assertSame(2, PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::PageViewed->value)
            ->where('path', '/sobre')
            ->count());
    }

    public function test_known_crawler_is_excluded_before_visitor_session_and_events_are_created(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
            ->get('/sobre')
            ->assertOk();

        self::assertSame(0, AnalyticsVisitor::query()->count());
        self::assertSame(0, AnalyticsSession::query()->count());
        self::assertSame(0, PlatformAnalyticsEvent::query()->count());
    }

    public function test_legacy_tool_url_produces_the_same_tool_open_event_as_canonical_routing(): void
    {
        $this->get('/ferramentas/calculadora-margem-markup')->assertOk();

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::ToolOpened->value,
            'channel' => 'tool',
            'subject_slug' => 'calculadora-margem-markup',
        ]);
    }
}
