<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculation_is_saved_and_can_be_listed_in_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tools.calculadora-de-honorarios-contabeis.calculate'), [
            'monthly_revenue' => '100.000,00', 'employees' => 3, 'partners' => 2,
            'monthly_invoices' => 80, 'monthly_bank_transactions' => 150,
            'tax_regime' => 'simples_nacional', 'business_segment' => 'services', 'complexity' => 'medium',
        ])->assertRedirect();

        $this->assertDatabaseHas('tool_runs', ['tool_slug' => 'calculadora-de-honorarios-contabeis', 'user_id' => $user->id, 'status' => ToolRunStatus::Succeeded->value]);
        $this->actingAs($user)->get(route('tools.calculadora-de-honorarios-contabeis.history.index'))->assertOk()->assertSee('Histórico de cálculos');
        $this->actingAs($user)->get(route('tools.calculadora-de-honorarios-contabeis.history.export'))->assertDownload('historico-honorarios-contabeis.csv');
    }

    public function test_owner_can_favorite_duplicate_and_delete_a_calculation(): void
    {
        $user = User::factory()->create();
        $run = $this->runFor($user, 'accounting_fee');

        $this->actingAs($user)->patch(route('tools.calculadora-de-honorarios-contabeis.history.favorite', $run->id))->assertRedirect();
        $this->assertDatabaseHas('tool_run_favorites', ['tool_run_id' => $run->id, 'user_id' => $user->id]);
        $this->actingAs($user)->post(route('tools.calculadora-de-honorarios-contabeis.history.duplicate', $run->id))->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'));
        $this->actingAs($user)->delete(route('tools.calculadora-de-honorarios-contabeis.history.delete', $run->id))->assertRedirect();
        $this->assertDatabaseMissing('tool_runs', ['id' => $run->id]);
    }

    public function test_user_cannot_change_another_users_calculation(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $run = $this->runFor($owner, 'accounting_fee');
        $this->actingAs($other)->patch(route('tools.calculadora-de-honorarios-contabeis.history.favorite', $run->id))->assertNotFound();
        $this->actingAs($other)->delete(route('tools.calculadora-de-honorarios-contabeis.history.delete', $run->id))->assertNotFound();
        $this->assertDatabaseHas('tool_runs', ['id' => $run->id]);
    }

    private function runFor(User $user, string $type): ToolRun
    {
        return ToolRun::query()->create([
            'user_id' => $user->id, 'tool_slug' => 'calculadora-de-honorarios-contabeis', 'tool_version' => '1.2.0',
            'schema_version' => 1, 'rule_version' => '1.0.0', 'reference_date' => now()->toDateString(),
            'status' => ToolRunStatus::Succeeded, 'input_payload' => ['run_type' => $type, 'monthly_revenue' => '50.000,00', 'tax_regime' => 'mei'],
            'result_payload' => ['recommended_fee' => 'R$ 500,00'], 'started_at' => now(), 'finished_at' => now(), 'expires_at' => now()->addYear(),
        ]);
    }
}
