<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Tools\Favorites\Models\UserToolFavorite;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Tools\ToolCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CatalogDiscoveryLot6Test extends TestCase
{
    use RefreshDatabase;

    public function test_full_catalog_search_accepts_terms_in_any_order_and_ranks_name_match_first(): void
    {
        $results = $this->app->make(ToolCatalog::class)->search('líquido salário');

        self::assertNotEmpty($results);
        self::assertSame('take-home-pay-calculator', $results->first()['slug']);
    }

    public function test_catalog_feeds_favorites_recent_and_featured_signals_to_smart_search(): void
    {
        $user = User::factory()->create();

        UserToolFavorite::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'take-home-pay-calculator',
        ]);

        ToolRun::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'working-capital-calculator',
            'tool_version' => '1.0.0',
            'rule_version' => '2026.1.0',
            'reference_date' => '2026-08-19',
            'status' => ToolRunStatus::Succeeded,
            'input_payload' => [],
            'result_payload' => [],
            'normative_references' => [],
            'started_at' => '2026-08-19 12:00:00',
            'finished_at' => '2026-08-19 12:01:00',
            'expires_at' => '2027-08-19 12:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('tools.index'))->assertOk();

        self::assertSame(['take-home-pay-calculator'], $response->viewData('favoriteToolSlugs')->all());
        self::assertContains('working-capital-calculator', $response->viewData('recentToolSlugs')->all());
        self::assertNotEmpty($response->viewData('featuredToolSlugs'));

        $response
            ->assertSee('Seus atalhos')
            ->assertSee('Favorita')
            ->assertSee('Usada recentemente')
            ->assertSee('take-home-pay-calculator', false)
            ->assertSee('working-capital-calculator', false);
    }

    public function test_personalized_shortcuts_do_not_compete_with_filtered_catalog_results(): void
    {
        $user = User::factory()->create();

        UserToolFavorite::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'take-home-pay-calculator',
        ]);

        $this->actingAs($user)
            ->get(route('tools.index', ['q' => 'fator r']))
            ->assertOk()
            ->assertDontSee('Seus atalhos');
    }
}
