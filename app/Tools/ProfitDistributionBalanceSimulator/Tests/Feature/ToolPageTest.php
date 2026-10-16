<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_loads(): void
    {
        $this->get('/tools/contabil/ferramentas/owner-distribution-planner')->assertOk()->assertSee('Balanço');
    }

    public function test_calculation_returns_result(): void
    {
        $this->post('/tools/contabil/ferramentas/owner-distribution-planner', $this->input())
            ->assertOk()->assertSee('Comparação estimada');
    }

    #[CoversPlusFeature('owner-distribution-planner', 'report')]
    public function test_report_can_be_downloaded_as_spreadsheet(): void
    {
        $this->get(route('tools.owner-distribution-planner.export', ['format' => 'xlsx', ...$this->input()]))
            ->assertOk()->assertHeader('content-disposition');
    }

    /** @return array<string, string> */
    private function input(): array
    {
        return ['annual_revenue' => '240.000,00', 'accounting_profit' => '72.000,00', 'reference_margin' => '32', 'taxes_on_revenue' => '14.400,00'];
    }
}
