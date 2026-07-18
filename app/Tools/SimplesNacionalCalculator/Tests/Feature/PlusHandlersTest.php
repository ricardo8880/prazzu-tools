<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Feature;

use App\Models\User;
use App\Tools\SimplesNacionalCalculator\Infrastructure\Models\SimplesNacionalCalculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlusHandlersTest extends TestCase
{
    use RefreshDatabase;

    public function test_alerts_route_executes_the_alert_analysis_handler(): void
    {
        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.alerts'), [
                'annex' => 'I',
                'rbt12' => '175.000,00',
                'monthly_revenue' => '15.000,00',
            ])
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('alerts_analysis', static fn (array $analysis): bool => count($analysis['alerts']) > 0
                && array_key_exists('warning', $analysis['summary'])
            );
    }

    public function test_authenticated_user_can_save_and_list_a_calculation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.history.store'), $this->historyPayload())
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('history_success');

        $this->assertDatabaseHas('simples_nacional_calculations', [
            'user_id' => $user->getAuthIdentifier(),
            'company_name' => 'Empresa Teste',
            'reference_month' => '2026-07-01',
            'annex' => 'I',
            'rbt12_cents' => 18_000_000,
            'monthly_revenue_cents' => 1_500_000,
            'estimated_das_cents' => 60_000,
        ]);

        $this->actingAs($user)
            ->get(route('tools.calculadora-simples-nacional.index'))
            ->assertOk()
            ->assertSee('Empresa Teste');
    }

    public function test_user_can_delete_only_their_own_calculation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('tools.calculadora-simples-nacional.plus.history.store'), $this->historyPayload());

        $calculation = SimplesNacionalCalculation::query()->sole();

        $this->actingAs($otherUser)
            ->delete(route('tools.calculadora-simples-nacional.plus.history.destroy', $calculation->getKey()))
            ->assertNotFound();

        $this->assertDatabaseHas('simples_nacional_calculations', ['id' => $calculation->getKey()]);

        $this->actingAs($owner)
            ->from(route('tools.calculadora-simples-nacional.index'))
            ->delete(route('tools.calculadora-simples-nacional.plus.history.destroy', $calculation->getKey()))
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('history_success');

        $this->assertDatabaseMissing('simples_nacional_calculations', ['id' => $calculation->getKey()]);
    }

    /** @return array<string, string> */
    private function historyPayload(): array
    {
        return [
            'company_name' => 'Empresa Teste',
            'reference_month' => '2026-07',
            'annex' => 'I',
            'rbt12' => '180.000,00',
            'monthly_revenue' => '15.000,00',
        ];
    }
}
