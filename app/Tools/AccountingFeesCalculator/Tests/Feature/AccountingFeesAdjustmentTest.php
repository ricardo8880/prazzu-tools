<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_adjustment_page_is_available(): void
    {
        $this->get(route('tools.calculadora-de-honorarios-contabeis.adjustments.index'))->assertOk()->assertSee('Reajuste de honorários');
    }

    public function test_it_calculates_and_stores_adjustment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [
            'scenario_label' => 'Renovação anual — cenário A', 'index_type' => 'ipca', 'reference_period' => '2026-07',
            'current_value' => '1.500,00', 'percentage' => '4.62', 'notes' => 'Índice acumulado do contrato.',
        ])->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.adjustments.index'))->assertSessionHas('adjustment_result.difference_cents', 6930);

        $run = ToolRun::query()->where('tool_slug', 'calculadora-de-honorarios-contabeis')->firstOrFail();
        self::assertSame('fee_adjustment', data_get($run->input_payload, 'run_type'));
        self::assertSame(156930, data_get($run->result_payload, 'adjusted_value_cents'));
    }

    public function test_it_validates_adjustment_data(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [])->assertSessionHasErrors(['scenario_label', 'index_type', 'reference_period', 'current_value', 'percentage']);
    }

    public function test_only_the_owner_can_delete_an_adjustment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $run = ToolRun::query()->create([
            'user_id' => $owner->id, 'tool_slug' => 'calculadora-de-honorarios-contabeis', 'tool_version' => '1.2.0', 'schema_version' => 1, 'rule_version' => 'fee-adjustment-v1',
            'reference_date' => '2026-07-01', 'status' => ToolRunStatus::Succeeded, 'input_payload' => ['run_type' => 'fee_adjustment', 'reference_period' => '2026-07'],
            'result_payload' => ['adjusted_value_cents' => 156930], 'started_at' => now(), 'finished_at' => now(), 'expires_at' => now()->addYear(),
        ]);
        $this->actingAs($other)->delete(route('tools.calculadora-de-honorarios-contabeis.adjustments.delete', $run->id))->assertNotFound();
        $this->actingAs($owner)->delete(route('tools.calculadora-de-honorarios-contabeis.adjustments.delete', $run->id))->assertRedirect();
        $this->assertDatabaseMissing('tool_runs', ['id' => $run->id]);
    }
}
