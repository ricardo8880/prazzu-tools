<?php

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Analytics\Concerns\ActsAsInternalAdministrator;
use Tests\TestCase;

final class HomeSearchAnalyticsTest extends TestCase
{
    use ActsAsInternalAdministrator, RefreshDatabase;

    public function test_home_search_is_recorded_even_when_there_are_no_results(): void
    {
        $response = $this->get('/ferramentas?q=ferramenta+que+ainda+nao+existe&source=home_search');

        $response->assertOk();

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', 'home.search.submitted')
            ->latest('occurred_at')
            ->firstOrFail();

        self::assertSame('ferramenta que ainda nao existe', data_get($event->metadata, 'query'));
        self::assertSame(0, data_get($event->metadata, 'result_count'));
        self::assertFalse((bool) data_get($event->metadata, 'has_results'));
    }

    public function test_home_search_records_result_count_when_matches_exist(): void
    {
        $response = $this->get('/ferramentas?q=salario&source=home_search');

        $response->assertOk();

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', 'home.search.submitted')
            ->latest('occurred_at')
            ->firstOrFail();

        self::assertGreaterThan(0, (int) data_get($event->metadata, 'result_count'));
        self::assertTrue((bool) data_get($event->metadata, 'has_results'));
    }

    public function test_catalog_search_not_originating_from_home_is_not_recorded_as_home_demand(): void
    {
        $this->get('/ferramentas?q=salario')->assertOk();

        self::assertFalse(
            PlatformAnalyticsEvent::query()
                ->where('event_name', 'home.search.submitted')
                ->exists()
        );
    }

    public function test_search_demand_is_visible_in_analytics_dashboard(): void
    {
        $this->get('/ferramentas?q=calculadora+espacial&source=home_search')->assertOk();

        $this->signInAsInternalAdministrator();

        $this->get(route('admin.analytics.index', ['period' => 'today']))
            ->assertOk()
            ->assertSee('Demandas de busca da página inicial')
            ->assertSee('calculadora espacial')
            ->assertSee('Nenhum');
    }
}
