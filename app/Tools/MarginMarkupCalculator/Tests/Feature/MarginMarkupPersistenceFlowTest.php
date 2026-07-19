<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarginMarkupPersistenceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_repeat_export_and_delete_owned_history(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $run = $this->createRun($user, salePrice: 'R$ 160,00');
        $otherRun = $this->createRun($otherUser, salePrice: 'R$ 999,00');

        $this->actingAs($user)
            ->get(route('tools.calculadora-margem-markup.history.index'))
            ->assertOk()
            ->assertSee('R$ 160,00')
            ->assertDontSee('R$ 999,00');

        $this->actingAs($user)
            ->get(route('tools.calculadora-margem-markup.history.show', $run))
            ->assertOk()
            ->assertSee('Detalhes do cálculo')
            ->assertSee('R$ 160,00');

        $this->actingAs($user)
            ->get(route('tools.calculadora-margem-markup.history.show', $otherRun))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('tools.calculadora-margem-markup.history.repeat', $run))
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasInput('base_cost', '100,00');

        $this->actingAs($user)
            ->get(route('tools.calculadora-margem-markup.history.pdf', $run))
            ->assertOk()
            ->assertSee('Relatório de Margem, Markup e Formação de Preço')
            ->assertSee('R$ 160,00');

        $this->actingAs($user)
            ->delete(route('tools.calculadora-margem-markup.history.destroy', $run))
            ->assertRedirect(route('tools.calculadora-margem-markup.history.index'));

        $this->assertDatabaseMissing('tool_runs', ['id' => $run->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'tool_run.deleted',
            'auditable_id' => $run->id,
            'actor_id' => $user->id,
        ]);
    }

    private function createRun(User $user, string $salePrice): ToolRun
    {
        return ToolRun::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'calculadora-margem-markup',
            'tool_version' => '1.0.0',
            'rule_version' => '2.0.0',
            'reference_date' => '2026-07-15',
            'status' => ToolRunStatus::Succeeded,
            'input_payload' => [
                'reference_date' => '2026-07-15',
                'base_cost' => '100,00',
                'desired_margin' => '20',
            ],
            'result_payload' => [
                'calculation_type' => 'single',
                'total_cost' => 'R$ 100,00',
                'sale_price' => $salePrice,
                'gross_profit' => 'R$ 60,00',
                'net_profit' => 'R$ 60,00',
                'taxes_amount' => 'R$ 0,00',
                'commission_amount' => 'R$ 0,00',
                'card_fees_amount' => 'R$ 0,00',
                'marketplace_fees_amount' => 'R$ 0,00',
                'margin' => '20%',
                'markup' => '60%',
                'markup_multiplier' => '1,60',
                'rule_version' => '2.0.0',
            ],
            'started_at' => now()->subSecond(),
            'finished_at' => now(),
            'expires_at' => now()->addDays(90),
        ]);
    }
}
