<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Feature;

use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LaborTerminationToolTest extends TestCase
{
    use RefreshDatabase;
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-de-rescisao.index'))
            ->assertOk()
            ->assertSee('Calculadora de Rescisão Trabalhista')
            ->assertSee('Dados da rescisão');
    }

    public function test_form_calculates_indemnified_notice_and_projection(): void
    {
        $this->post(route('tools.calculadora-de-rescisao.calculate'), [
            'monthly_salary' => '3.000,00',
            'admission_date' => '2024-01-10',
            'termination_date' => '2026-07-14',
            'termination_type' => 'dismissal_without_cause',
            'contract_type' => 'indefinite',
            'notice_type' => 'indemnified',
            'days_worked_in_month' => 14,
            'has_overdue_vacation' => 1,
            'fgts_balance' => '10.000,00',
            'other_discounts' => '0,00',
            'dependents' => 0,
        ])->assertRedirect(route('tools.calculadora-de-rescisao.index'))
            ->assertSessionHas('calculation_result', fn (array $result): bool =>
                $result['notice_pay'] === 'R$ 3.600,00'
                && $result['notice_days'] === 36
                && $result['projected_termination_date'] === '19/08/2026'
                && $result['termination_type_label'] === 'Dispensa sem justa causa'
            )->assertSessionHasNoErrors();
    }

    public function test_incompatible_notice_is_returned_as_validation_error(): void
    {
        $this->from(route('tools.calculadora-de-rescisao.index'))
            ->post(route('tools.calculadora-de-rescisao.calculate'), [
                'monthly_salary' => '3.000,00',
                'admission_date' => '2026-01-10',
                'termination_date' => '2026-07-14',
                'termination_type' => 'dismissal_with_cause',
                'contract_type' => 'indefinite',
                'notice_type' => 'indemnified',
                'days_worked_in_month' => 14,
                'has_overdue_vacation' => 0,
            'fgts_balance' => '0,00',
            'other_discounts' => '0,00',
            'dependents' => 0,
            ])->assertRedirect(route('tools.calculadora-de-rescisao.index'))
            ->assertSessionHasErrors(['notice_type']);
    }

    public function test_authenticated_calculation_is_saved_and_can_be_repeated_and_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tools.calculadora-de-rescisao.calculate'), [
            'monthly_salary' => '3.000,00',
            'admission_date' => '2024-01-10',
            'termination_date' => '2026-07-14',
            'termination_type' => 'dismissal_without_cause',
            'contract_type' => 'indefinite',
            'notice_type' => 'indemnified',
            'days_worked_in_month' => 14,
            'overdue_vacation_periods' => 1,
            'double_vacation_periods' => 0,
            'fgts_balance' => '10.000,00',
            'domestic_indemnity_reserve_balance' => '0,00',
            'other_discounts' => '0,00',
            'dependents' => 0,
            'commission_average' => '0,00',
            'overtime_average' => '0,00',
            'recurring_additions' => '0,00',
            'article_480_discount' => '0,00',
            'extraordinary_indemnities' => '0,00',
        ])->assertSessionHas('history_saved', true);

        $run = ToolRun::query()->sole();
        $this->assertSame($user->id, $run->user_id);
        $this->assertSame('R$ 3.000,00', $run->input_payload['monthly_salary']);
        $this->assertArrayHasKey('net_total', $run->result_payload);

        $this->actingAs($user)->get(route('tools.calculadora-de-rescisao.history.index'))
            ->assertOk()
            ->assertSee('Histórico de cálculos');

        $this->actingAs($user)->post(route('tools.calculadora-de-rescisao.history.repeat', $run))
            ->assertRedirect(route('tools.calculadora-de-rescisao.index'))
            ->assertSessionHasInput('monthly_salary', '3.000,00');

        $this->actingAs($user)->delete(route('tools.calculadora-de-rescisao.history.destroy', $run))
            ->assertRedirect(route('tools.calculadora-de-rescisao.history.index'));

        $this->assertDatabaseCount('tool_runs', 0);
    }

    public function test_guest_calculation_is_not_saved_to_history(): void
    {
        $this->post(route('tools.calculadora-de-rescisao.calculate'), [
            'monthly_salary' => '3.000,00',
            'admission_date' => '2024-01-10',
            'termination_date' => '2026-07-14',
            'termination_type' => 'dismissal_without_cause',
            'contract_type' => 'indefinite',
            'notice_type' => 'indemnified',
            'days_worked_in_month' => 14,
            'overdue_vacation_periods' => 0,
            'double_vacation_periods' => 0,
            'fgts_balance' => '0,00',
            'domestic_indemnity_reserve_balance' => '0,00',
            'other_discounts' => '0,00',
            'dependents' => 0,
            'commission_average' => '0,00',
            'overtime_average' => '0,00',
            'recurring_additions' => '0,00',
            'article_480_discount' => '0,00',
            'extraordinary_indemnities' => '0,00',
        ]);

        $this->assertDatabaseCount('tool_runs', 0);
    }

    public function test_current_calculation_can_be_exported_as_printable_pdf_report(): void
    {
        $this->post(route('tools.calculadora-de-rescisao.export'), [
            'monthly_salary' => '3.000,00',
            'admission_date' => '2024-01-10',
            'termination_date' => '2026-07-14',
            'termination_type' => 'dismissal_without_cause',
            'contract_type' => 'indefinite',
            'notice_type' => 'indemnified',
            'days_worked_in_month' => 14,
            'overdue_vacation_periods' => 0,
            'double_vacation_periods' => 0,
            'fgts_balance' => '0,00',
            'domestic_indemnity_reserve_balance' => '0,00',
            'other_discounts' => '0,00',
            'dependents' => 0,
            'commission_average' => '0,00',
            'overtime_average' => '0,00',
            'recurring_additions' => '0,00',
            'article_480_discount' => '0,00',
            'extraordinary_indemnities' => '0,00',
        ])->assertOk()
            ->assertSee('Relatório de Rescisão Trabalhista')
            ->assertSee('Imprimir / Salvar como PDF')
            ->assertSee('R$ 3.000,00');
    }

    public function test_user_can_export_owned_history_report(): void
    {
        $user = User::factory()->create();
        $run = ToolRun::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'calculadora-de-rescisao',
            'tool_version' => '1.0.0',
            'rule_version' => '1.4.0',
            'reference_date' => '2026-07-14',
            'status' => \App\Core\Tools\History\Enums\ToolRunStatus::Succeeded,
            'input_payload' => ['admission_date' => '2024-01-10', 'termination_date' => '2026-07-14'],
            'result_payload' => ['termination_type_label' => 'Dispensa sem justa causa', 'net_total' => 'R$ 5.000,00'],
            'started_at' => now(),
            'finished_at' => now(),
            'expires_at' => now()->addDays(180),
        ]);

        $this->actingAs($user)
            ->get(route('tools.calculadora-de-rescisao.history.pdf', $run))
            ->assertOk()
            ->assertSee('Relatório de Rescisão Trabalhista')
            ->assertSee('R$ 5.000,00');
    }

}
