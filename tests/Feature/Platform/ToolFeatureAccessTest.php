<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Access\Enums\CommercialAccessMode;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Tools\ToolRegistry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ToolFeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_essential_calculation_remains_complete_for_a_visitor_when_monetized(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::Monetized->value);

        $this->post(route('tools.calculadora-simples-nacional.calculate'), [
            'annex' => 'I',
            'rbt12' => '180.000,00',
            'monthly_revenue' => '15.000,00',
        ])->assertOk()->assertHeaderMissing('X-Prazzu-Access-Reason');
    }

    public function test_plus_feature_requires_a_plus_plan_when_monetized(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::Monetized->value);

        $this->post(route('tools.calculadora-simples-nacional.plus.alerts'), $this->alertPayload())
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('access_warning')
            ->assertHeader('X-Prazzu-Access-Reason', 'feature.authentication_required');

        $freeUser = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
        $this->actingAs($freeUser)
            ->post(route('tools.calculadora-simples-nacional.plus.alerts'), $this->alertPayload())
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('access_warning')
            ->assertHeader('X-Prazzu-Access-Reason', 'feature.plus_required');

        $plusUser = User::factory()->create(['subscription_plan' => SubscriptionPlan::Plus]);
        $this->actingAs($plusUser)
            ->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.alerts'), $this->alertPayload())
            ->assertOk()
            ->assertSessionMissing('access_warning');
    }

    public function test_launch_policy_temporarily_releases_plus_to_visitors(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::LaunchFree->value);

        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.alerts'), $this->alertPayload())
            ->assertOk()
            ->assertSessionMissing('access_warning');
    }

    public function test_launch_free_allows_every_declared_tool_feature_for_visitors(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::LaunchFree->value);
        $registry = app(ToolRegistry::class);
        $gate = app(ToolFeatureAccessGate::class);

        foreach ($registry->manifests(false) as $manifest) {
            foreach ($manifest->features as $feature) {
                self::assertTrue(
                    $gate->decide($manifest, $feature->key, null)->allowed,
                    "O recurso [{$manifest->slug}:{$feature->key}] deve permanecer público durante launch_free.",
                );
            }
        }
    }

    public function test_disabled_feature_returns_service_unavailable_in_every_commercial_mode(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::LaunchFree->value);
        config()->set('features.tools.calculadora-simples-nacional.features.alerts.enabled', false);

        $this->post(route('tools.calculadora-simples-nacional.plus.alerts'), $this->alertPayload())
            ->assertServiceUnavailable();
    }

    public function test_middleware_returns_not_found_for_an_undeclared_feature_key(): void
    {
        Route::middleware('tool.feature:calculadora-simples-nacional,unknown_feature')
            ->get('/_test/unknown-tool-feature', static fn (): string => 'unreachable');

        $this->get('/_test/unknown-tool-feature')->assertNotFound();
    }

    public function test_essential_calculation_only_persists_history_when_the_identity_can_use_plus(): void
    {
        config()->set('access.commercial_mode', CommercialAccessMode::Monetized->value);
        $freeUser = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);

        $this->actingAs($freeUser)
            ->post(route('tools.calculadora-margem-markup.calculate'), $this->marginPayload())
            ->assertOk();

        $this->assertDatabaseMissing('tool_runs', [
            'user_id' => $freeUser->getAuthIdentifier(),
            'tool_slug' => 'calculadora-margem-markup',
        ]);

        $plusUser = User::factory()->create(['subscription_plan' => SubscriptionPlan::Plus]);
        $this->actingAs($plusUser)
            ->post(route('tools.calculadora-margem-markup.calculate'), $this->marginPayload())
            ->assertOk();

        $this->assertDatabaseHas('tool_runs', [
            'user_id' => $plusUser->getAuthIdentifier(),
            'tool_slug' => 'calculadora-margem-markup',
        ]);
    }

    /** @return array<string, string> */
    private function alertPayload(): array
    {
        return [
            'annex' => 'I',
            'rbt12' => '175.000,00',
            'monthly_revenue' => '15.000,00',
        ];
    }

    /** @return array<string, string> */
    private function marginPayload(): array
    {
        return [
            'reference_date' => '2026-07-18',
            'base_cost' => '100,00',
            'additional_costs' => '20,00',
            'desired_margin' => '25',
        ];
    }
}
