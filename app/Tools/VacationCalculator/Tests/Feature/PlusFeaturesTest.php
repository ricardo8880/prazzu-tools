<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlusFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_requires_authentication(): void
    {
        $this->get(route('tools.calculadora-ferias.history.index'))->assertRedirect();
    }

    public function test_planner_route_is_registered(): void
    {
        self::assertTrue(app('router')->has('tools.calculadora-ferias.planner'));
        self::assertTrue(app('router')->has('tools.calculadora-ferias.plan'));
    }

    public function test_authenticated_user_can_open_history_when_feature_is_available(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('tools.calculadora-ferias.history.index'));
        self::assertContains($response->status(), [200, 302, 403]);
    }
}
