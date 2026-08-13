<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void
    {
        $this->get(route('tools.calculadora-depreciacao-ativos.index'))
            ->assertOk()
            ->assertSee('Calculadora de Depreciação de Ativos')
            ->assertSee('Prazzu Plus');
    }

    public function test_linear_calculation_renders_expected_values(): void
    {
        $this->post(route('tools.calculadora-depreciacao-ativos.calculate'), [
            'asset_name' => 'Notebook',
            'asset_value' => '12.000,00',
            'useful_life_years' => 5,
            'method' => 'linear',
        ])->assertOk()->assertSee('R$ 200,00')->assertSee('R$ 2.400,00')->assertSee('R$ 9.600,00')->assertSee('Memória de cálculo');
    }

    #[CoversPlusFeature('calculadora-depreciacao-ativos', 'multiple_assets')]
    #[CoversPlusFeature('calculadora-depreciacao-ativos', 'methods')]
    #[CoversPlusFeature('calculadora-depreciacao-ativos', 'export')]
    public function test_multiple_assets_render_consolidated_projection(): void
    {
        $this->post(route('tools.calculadora-depreciacao-ativos.calculate'), [
            'asset_name' => 'Máquina',
            'asset_value' => '10.000,00',
            'useful_life_years' => 5,
            'method' => 'declining_balance',
            'assets' => [[
                'name' => 'Notebook', 'value' => '6.000,00', 'useful_life_years' => 3, 'method' => 'linear',
            ]],
        ])->assertOk()
            ->assertSee('Projeção patrimonial consolidada')
            ->assertSee('R$ 16.000,00')
            ->assertSee('Exportar PDF')
            ->assertSee('Baixar XLSX');
    }
}
