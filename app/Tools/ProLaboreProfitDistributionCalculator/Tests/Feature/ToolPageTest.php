<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_exposes_the_complete_essential_form(): void
    {
        $this->get(route('tools.calculadora-pro-labore-distribuicao-lucros.index'))
            ->assertOk()
            ->assertSee('Calculadora de Pró-Labore e Distribuição de Lucros')
            ->assertSee('Pró-labore bruto')
            ->assertSee('Lucro contábil do período')
            ->assertSee('Distribuição pretendida');
    }

    public function test_it_calculates_and_displays_the_consolidated_result(): void
    {
        $this->post(route('tools.calculadora-pro-labore-distribuicao-lucros.calculate'), [
            'competence' => '2026-01',
            'company_regime' => 'simples_annex_iv',
            'partner_label' => 'Sócio A',
            'gross_pro_labore' => '5000,00',
            'dependents' => 0,
            'other_official_social_security' => '0,00',
            'ownership_percentage' => '100',
            'accounting_profit' => '20000,00',
            'accumulated_losses' => '0,00',
            'reserves_and_unavailable_amounts' => '0,00',
            'adjustments' => '0,00',
            'prior_distributions' => '0,00',
            'intended_distribution' => '12000,00',
            'confirm_assumptions' => '1',
        ])->assertOk()
            ->assertSee('Resultado consolidado')
            ->assertSee('R$ 12.000,00')
            ->assertSee('R$ 16.450,00');
    }

    public function test_it_rejects_unsupported_competence_and_missing_confirmation(): void
    {
        $this->from(route('tools.calculadora-pro-labore-distribuicao-lucros.index'))
            ->post(route('tools.calculadora-pro-labore-distribuicao-lucros.calculate'), [
                'competence' => '2025-12',
                'company_regime' => 'simples_annex_iv',
                'gross_pro_labore' => '5000,00',
                'ownership_percentage' => '100',
                'accounting_profit' => '10000,00',
                'intended_distribution' => '5000,00',
            ])->assertRedirect()
            ->assertSessionHasErrors(['competence', 'confirm_assumptions']);
    }

    public function test_advanced_simulation_route_accepts_multiple_scenarios(): void
    {
        $period = [
            'competence' => '2026-01', 'company_regime' => 'simples_outside_annex_iv',
            'accounting_profit' => '20000', 'accumulated_losses' => '0',
            'reserves_and_unavailable_amounts' => '0', 'adjustments' => '0',
            'prior_distributions' => '0', 'intended_distribution' => '10000',
            'partners' => [
                ['label' => 'A', 'ownership_percentage' => '60', 'gross_pro_labore' => '5000', 'dependents' => 0, 'other_official_social_security' => '0'],
                ['label' => 'B', 'ownership_percentage' => '40', 'gross_pro_labore' => '3000', 'dependents' => 0, 'other_official_social_security' => '0'],
            ],
        ];

        $response = $this->post(route('tools.calculadora-pro-labore-distribuicao-lucros.simulate'), [
            'scenarios' => [
                ['name' => 'Base', 'periods' => [$period]],
                ['name' => 'Alternativo', 'periods' => [$period]],
            ],
            'confirm_simulation_assumptions' => '1',
        ]);

        $response->assertOk()->assertSee('Comparação de cenários');
    }
}
