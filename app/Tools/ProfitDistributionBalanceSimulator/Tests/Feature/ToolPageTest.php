<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_loads(): void
    {
        $this->get(route('tools.owner-distribution-planner.index'))
            ->assertOk()
            ->assertSee('Owner Distribution Planner');
    }

    public function test_calculation_returns_result(): void
    {
        $this->post(route('tools.owner-distribution-planner.calculate'), $this->input())
            ->assertOk()
            ->assertSee('Planning result');
    }

    #[CoversPlusFeature('owner-distribution-planner', 'report')]
    public function test_report_can_be_downloaded_as_spreadsheet(): void
    {
        $this->get(route('tools.owner-distribution-planner.export', ['format' => 'xlsx', ...$this->input()]))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    /** @return array<string, string> */
    private function input(): array
    {
        return [
            'retained_earnings' => '40,000.00',
            'current_year_net_income' => '32,000.00',
            'prior_distributions' => '10,000.00',
            'cash_available' => '80,000.00',
            'minimum_cash_reserve' => '20,000.00',
            'planned_distribution' => '50,000.00',
        ];
    }
}
