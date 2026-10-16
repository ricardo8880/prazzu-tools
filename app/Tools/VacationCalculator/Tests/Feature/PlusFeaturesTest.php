<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlusFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_requires_authentication(): void
    {
        $this->get(route('tools.pto-calculator.history.index'))->assertRedirect();
    }

    public function test_planner_route_is_registered(): void
    {
        self::assertTrue(app('router')->has('tools.pto-calculator.planner'));
        self::assertTrue(app('router')->has('tools.pto-calculator.plan'));
    }

    #[CoversPlusFeature('pto-calculator', 'history')]
    public function test_authenticated_user_can_open_history_when_feature_is_available(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('tools.pto-calculator.history.index'))
            ->assertOk()
            ->assertSee('Histórico de férias');
    }
}
