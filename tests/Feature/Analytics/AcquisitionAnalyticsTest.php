<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Infrastructure\Http\AcquisitionResolver;
use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsVisitor;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AcquisitionAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_it_classifies_search_ai_social_campaign_and_referral_sources(): void
    {
        $resolver = app(AcquisitionResolver::class);

        $this->assertSame(['google', 'organic'], $this->source($resolver, 'https://google.com/search?q=prazzu'));
        $this->assertSame(['chatgpt', 'ai'], $this->source($resolver, 'https://chatgpt.com/'));
        $this->assertSame(['instagram', 'social'], $this->source($resolver, 'https://instagram.com/'));
        $this->assertSame(['example.com', 'referral'], $this->source($resolver, 'https://example.com/page'));

        $request = Request::create('/?utm_source=newsletter&utm_medium=email&utm_campaign=julho');
        $acquisition = $resolver->resolve($request);
        $this->assertSame('newsletter', $acquisition->source);
        $this->assertSame('email', $acquisition->medium);
        $this->assertSame('julho', $acquisition->campaign);
    }

    public function test_dashboard_displays_sources_campaigns_attribution_and_funnels(): void
    {
        CarbonImmutable::setTestNow('2026-07-15 12:00:00');
        $visitor = (string) Str::uuid();
        $session = (string) Str::uuid();

        AnalyticsVisitor::query()->create([
            'id' => $visitor, 'first_seen_at' => now(), 'last_seen_at' => now(),
            'first_source' => 'google', 'first_medium' => 'organic',
            'last_source' => 'newsletter', 'last_medium' => 'email',
        ]);
        AnalyticsSession::query()->create([
            'id' => $session, 'visitor_id' => $visitor, 'started_at' => now(), 'last_activity_at' => now(),
            'source' => 'newsletter', 'medium' => 'email', 'campaign' => 'julho',
        ]);

        foreach (['page.viewed', 'blog_post_view', 'blog_tool_click', 'tool.calculation.completed', 'account.created', 'subscription.started', 'result.exported'] as $name) {
            PlatformAnalyticsEvent::query()->create([
                'event_id' => (string) Str::uuid(), 'event_name' => $name, 'schema_version' => 1,
                'channel' => 'platform', 'visitor_id' => $visitor, 'analytics_session_id' => $session,
                'source' => 'newsletter', 'medium' => 'email', 'campaign' => 'julho', 'occurred_at' => now(),
            ]);
        }

        $this->get(route('admin.analytics.acquisition', ['period' => 'today']))
            ->assertOk()->assertSee('Aquisição')->assertSee('newsletter')->assertSee('julho')
            ->assertSee('Primeira origem')->assertSee('Origem da assinatura')->assertSee('Funis por origem');
    }

    /** @return array{string,string} */
    private function source(AcquisitionResolver $resolver, string $referrer): array
    {
        $request = Request::create('/');
        $request->headers->set('referer', $referrer);
        $acquisition = $resolver->resolve($request);

        return [$acquisition->source, $acquisition->medium];
    }
}
