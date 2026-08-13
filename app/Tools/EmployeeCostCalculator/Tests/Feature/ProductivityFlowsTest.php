<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Core\ToolProfiles\Models\ToolCompanyProfile;
use App\Core\ToolProfiles\Models\ToolEmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductivityFlowsTest extends TestCase
{
    use RefreshDatabase;

    #[CoversPlusFeature('custo-funcionario-clt', 'company_profiles')]
    #[CoversPlusFeature('custo-funcionario-clt', 'employee_profiles')]
    public function test_authenticated_user_can_save_reusable_company_and_employee_profiles(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('tools.custo-funcionario-clt.profiles.companies.store'), [
            'name' => 'Empresa Exemplo', 'legal_name' => 'Empresa Exemplo Ltda.', 'document' => '12.345.678/0001-90',
        ])->assertRedirect(route('tools.custo-funcionario-clt.index'));
        $this->actingAs($user)->post(route('tools.custo-funcionario-clt.profiles.employees.store'), [
            'name' => 'Ana Lima', 'department' => 'Contábil', 'role' => 'Analista',
            'salary' => '5.000,00', 'variable_pay' => '0,00', 'benefits' => '800,00',
            'regime' => 'general', 'rat' => '1', 'third_parties' => '5.8', 'monthly_hours' => '220',
        ])->assertRedirect(route('tools.custo-funcionario-clt.index'));

        self::assertSame('Empresa Exemplo', ToolCompanyProfile::query()->where('user_id', $user->id)->sole()->name);
        self::assertSame('Ana Lima', ToolEmployeeProfile::query()->where('user_id', $user->id)->sole()->name);
    }

    #[CoversPlusFeature('custo-funcionario-clt', 'history')]
    public function test_authenticated_calculation_is_persisted_and_listed_in_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('tools.custo-funcionario-clt.calculate'), $this->employeePayload())->assertOk();
        $this->actingAs($user)->get(route('tools.custo-funcionario-clt.history.index'))
            ->assertOk()->assertSee('Histórico de custos CLT')->assertSee('Ana Lima');
        $this->assertDatabaseHas('tool_runs', ['tool_slug' => 'custo-funcionario-clt', 'user_id' => $user->id]);
    }

    #[CoversPlusFeature('custo-funcionario-clt', 'employment_model_comparison')]
    public function test_clt_pj_and_autonomous_models_are_compared_numerically(): void
    {
        $this->post(route('tools.custo-funcionario-clt.models.compare'), [
            ...$this->employeePayload(), 'clt_employee_discount_rate' => '11',
            'pj_monthly_invoice' => '8.000,00', 'pj_tax_rate' => '10', 'pj_expenses' => '500,00',
            'autonomous_gross' => '8.000,00', 'autonomous_discount_rate' => '20', 'autonomous_employer_rate' => '20',
        ])->assertOk()->assertSee('Comparação numérica das modalidades')->assertSee('CLT')->assertSee('PJ')->assertSee('Autônomo');
    }

    /** @return array<string, string> */
    private function employeePayload(): array
    {
        return [
            'employee_name' => 'Ana Lima', 'department' => 'Contábil', 'salary' => '5.000,00',
            'variable_pay' => '0,00', 'benefits' => '800,00', 'regime' => 'general',
            'rat' => '1', 'third_parties' => '5.8', 'monthly_hours' => '220',
        ];
    }
}
