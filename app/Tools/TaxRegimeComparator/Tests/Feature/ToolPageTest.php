<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Http\Middleware\EnsureToolFeatureAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureToolFeatureAccess::class);
    }

    public function test_tool_page_is_available_with_complete_form(): void
    {
        $this->get(route('tools.comparador-tributario.index'))
            ->assertOk()
            ->assertSee('Comparador Tributário')
            ->assertSee('Dados do cenário')
            ->assertSee('Simples Nacional')
            ->assertSee('Lucro Presumido')
            ->assertSee('Lucro Real')
            ->assertSee('Comparar regimes');
    }

    public function test_comparison_requires_the_core_scenario_fields(): void
    {
        $this->post(route('tools.comparador-tributario.compare'), [])
            ->assertSessionHasErrors([
                'reference_date',
                'business_activity',
                'monthly_revenue',
                'revenue_last_twelve_months',
                'payroll_last_twelve_months',
                'monthly_operating_costs',
                'monthly_deductible_expenses',
            ]);
    }

    public function test_valid_scenario_displays_presented_result_without_redirect(): void
    {
        $this->post(route('tools.comparador-tributario.compare'), [
            'reference_date' => '2025-07-01',
            'business_activity' => 'services',
            'monthly_revenue' => '100.000,00',
            'revenue_last_twelve_months' => '1.200.000,00',
            'payroll_last_twelve_months' => '360.000,00',
            'monthly_operating_costs' => '20.000,00',
            'monthly_deductible_expenses' => '10.000,00',
            'monthly_pis_cofins_credit_base' => '20.000,00',
            'indirect_tax_rate' => '5,00',
            'state' => 'sp',
            'municipality' => 'São Paulo',
        ])->assertOk()
            ->assertSee('Resultado da comparação');
    }

    #[CoversPlusFeature('comparador-tributario', 'export')]
    public function test_structured_comparison_can_be_exported_as_json(): void
    {
        $this->post(route('tools.comparador-tributario.export', 'json'), $this->validScenario())
            ->assertOk()->assertDownload('comparacao-tributaria.json');
    }

    #[CoversPlusFeature('comparador-tributario', 'professional_report')]
    public function test_professional_comparison_report_can_be_downloaded(): void
    {
        $this->post(route('tools.comparador-tributario.document', 'xlsx'), $this->validScenario())
            ->assertOk()->assertHeader('content-disposition');
    }

    #[CoversPlusFeature('comparador-tributario', 'history')]
    public function test_authenticated_comparison_is_saved_and_listed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('tools.comparador-tributario.compare'), $this->validScenario())->assertOk();
        $this->actingAs($user)->get(route('tools.comparador-tributario.history.index'))
            ->assertOk()->assertSee('Histórico do Comparador Tributário');
        $this->assertDatabaseHas('tool_runs', ['tool_slug' => 'comparador-tributario', 'user_id' => $user->id]);
    }

    /** @return array<string, string> */
    private function validScenario(): array
    {
        return [
            'reference_date' => '2025-07-01', 'business_activity' => 'services',
            'monthly_revenue' => '100.000,00', 'revenue_last_twelve_months' => '1.200.000,00',
            'payroll_last_twelve_months' => '360.000,00', 'monthly_operating_costs' => '20.000,00',
            'monthly_deductible_expenses' => '10.000,00', 'monthly_pis_cofins_credit_base' => '20.000,00',
            'indirect_tax_rate' => '5,00', 'state' => 'sp', 'municipality' => 'São Paulo',
        ];
    }
}
