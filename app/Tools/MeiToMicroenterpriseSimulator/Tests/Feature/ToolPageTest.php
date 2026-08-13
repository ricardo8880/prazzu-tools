<?php

declare(strict_types=1);

namespace App\Tools\MeiToMicroenterpriseSimulator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_loads(): void
    {
        $this->get('/tools/contabil/ferramentas/simulador-mei-microempresa')->assertOk()->assertSee('Simulador MEI');
    }

    public function test_calculation_returns_result(): void
    {
        $this->post('/tools/contabil/ferramentas/simulador-mei-microempresa', $this->input())
            ->assertOk()->assertSee('Impacto estimado da saída do MEI');
    }

    #[CoversPlusFeature('simulador-mei-microempresa', 'report')]
    public function test_report_can_be_downloaded_as_spreadsheet(): void
    {
        $this->get(route('tools.simulador-mei-microempresa.export', ['format' => 'xlsx', ...$this->input()]))
            ->assertOk()->assertHeader('content-disposition');
    }

    /** @return array<string, int|string> */
    private function input(): array
    {
        return ['current_annual_revenue' => '72.000,00', 'projected_annual_revenue' => '96.000,00', 'me_effective_tax_rate' => '6', 'monthly_accounting_cost' => '500,00', 'monthly_other_cost' => '250,00', 'annual_growth_rate' => '10', 'projection_years' => 3, 'target_fixed_cost_burden' => '5'];
    }
}
