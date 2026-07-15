<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\AnalyticsInsight;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class InsightsAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_and_displays_a_traffic_alert(): void
    {
        foreach (range(1, 20) as $index) {
            PlatformAnalyticsEvent::query()->create(['event_id'=>(string) Str::uuid(),'event_name'=>'page.viewed','schema_version'=>1,'channel'=>'platform','path'=>'/blog/teste','occurred_at'=>now()->subDays(10)->addMinutes($index),'metadata'=>[]]);
        }
        foreach (range(1, 5) as $index) {
            PlatformAnalyticsEvent::query()->create(['event_id'=>(string) Str::uuid(),'event_name'=>'page.viewed','schema_version'=>1,'channel'=>'platform','path'=>'/blog/teste','occurred_at'=>now()->subDays(2)->addMinutes($index),'metadata'=>[]]);
        }

        $this->withoutMiddleware()->get(route('admin.analytics.insights', ['period'=>'7']))
            ->assertOk()->assertSee('Insights inteligentes')->assertSee('Queda relevante de tráfego');
        $this->assertDatabaseHas('analytics_insights', ['type'=>'alert','metric_name'=>'page_views']);
    }

    public function test_an_insight_status_can_be_updated(): void
    {
        $insight = AnalyticsInsight::query()->create(['fingerprint'=>hash('sha256','test'),'type'=>'alert','severity'=>'warning','title'=>'Teste','message'=>'Mensagem','status'=>'open','period_start'=>now()->subDays(6)->toDateString(),'period_end'=>now()->toDateString(),'generated_at'=>now()]);
        $this->withoutMiddleware()->patch(route('admin.analytics.insights.status',$insight), ['status'=>'resolved'])->assertRedirect();
        $this->assertDatabaseHas('analytics_insights',['id'=>$insight->id,'status'=>'resolved']);
    }
}
