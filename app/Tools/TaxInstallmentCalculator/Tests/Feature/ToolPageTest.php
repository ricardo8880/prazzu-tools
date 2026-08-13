<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_available(): void
    {
        $this->get(route('tools.calculadora-parcelamento-tributario.index'))
            ->assertOk()->assertSee('Calculadora de Parcelamento Tributário')->assertSee('Prazzu Plus');
    }

    public function test_basic_calculation_renders_expected_values(): void
    {
        $this->post(route('tools.calculadora-parcelamento-tributario.calculate'), [
            'debt_amount' => '12.000,00', 'installments' => 12, 'monthly_charge' => '1,00', 'entry_amount' => '0,00',
        ])->assertOk()->assertSee('R$ 1.065,00')->assertSee('R$ 780,00')->assertSee('R$ 12.780,00')->assertSee('Memória de cálculo');
    }

    #[CoversPlusFeature('calculadora-parcelamento-tributario', 'scenario_comparison')]
    #[CoversPlusFeature('calculadora-parcelamento-tributario', 'export')]
    public function test_plus_scenario_renders_comparison(): void
    {
        $this->post(route('tools.calculadora-parcelamento-tributario.calculate'), [
            'debt_amount' => '10.000,00', 'installments' => 10, 'monthly_charge' => '1,00', 'entry_amount' => '0,00',
            'scenarios' => [['name' => 'Com entrada', 'entry_amount' => '2.000,00', 'installments' => 8, 'monthly_charge' => '1,00']],
        ])->assertOk()
            ->assertSee('Comparação de cenários')
            ->assertSee('Com entrada')
            ->assertSee('Evolução do saldo e cronograma')
            ->assertSee('Exportar relatório PDF')
            ->assertSee('Baixar cronograma XLSX');
    }
}
