<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void
    {
        $this->get(route('tools.asset-depreciation-calculator.index'))
            ->assertOk()
            ->assertSee('Asset Depreciation Calculator')
            ->assertSee('Prazzu Plus');
    }

    public function test_linear_calculation_renders_expected_values(): void
    {
        $this->post(route('tools.asset-depreciation-calculator.calculate'), [
            'asset_name' => 'Notebook',
            'asset_value' => '12.000,00',
            'useful_life_years' => 5,
            'method' => 'linear',
        ])->assertOk()->assertSee('$200.00')->assertSee('$2,400.00')->assertSee('$9,600.00')->assertSee('Calculation details');
    }

    #[CoversPlusFeature('asset-depreciation-calculator', 'multiple_assets')]
    #[CoversPlusFeature('asset-depreciation-calculator', 'methods')]
    #[CoversPlusFeature('asset-depreciation-calculator', 'export')]
    public function test_multiple_assets_render_consolidated_projection(): void
    {
        $this->post(route('tools.asset-depreciation-calculator.calculate'), [
            'asset_name' => 'Máquina',
            'asset_value' => '10.000,00',
            'useful_life_years' => 5,
            'method' => 'declining_balance',
            'assets' => [[
                'name' => 'Notebook', 'value' => '6.000,00', 'useful_life_years' => 3, 'method' => 'linear',
            ]],
        ])->assertOk()
            ->assertSee('Consolidated asset projection')
            ->assertSee('$16,000.00')
            ->assertSee('Export PDF')
            ->assertSee('Download XLSX');
    }
}
