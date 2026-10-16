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

    #[CoversPlusFeature('pto-calculator', 'history')]
    public function test_authenticated_user_can_open_pto_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('tools.pto-calculator.history.index'))
            ->assertOk()
            ->assertSee('PTO calculation history');
    }

    #[CoversPlusFeature('pto-calculator', 'professional_export')]
    public function test_professional_export_route_is_feature_gated(): void
    {
        self::assertTrue(app('router')->has('tools.pto-calculator.export'));
        self::assertTrue(app('router')->has('tools.pto-calculator.history.export'));
    }
}
