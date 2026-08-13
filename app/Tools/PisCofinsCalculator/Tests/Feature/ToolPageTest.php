<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-pis-cofins.index'))
            ->assertOk()
            ->assertSee('Calculadora PIS e COFINS')
            ->assertSee('Cumulativo')
            ->assertSee('Não cumulativo');
    }

    public function test_zero_base_is_rejected(): void
    {
        $this->post(route('tools.calculadora-pis-cofins.calculate'), $this->payload())
            ->assertSessionHasErrors('taxable_revenue');
    }

    public function test_cumulative_calculation_returns_expected_total(): void
    {
        $payload = $this->payload();
        $payload['taxable_revenue'] = '10.000,00';

        $this->post(route('tools.calculadora-pis-cofins.calculate'), $payload)
            ->assertOk()
            ->assertSee('R$ 365,00')
            ->assertSee('Memória de cálculo');
    }

    #[CoversPlusFeature('calculadora-pis-cofins', 'aggregate_credits')]
    #[CoversPlusFeature('calculadora-pis-cofins', 'multiple_operations')]
    #[CoversPlusFeature('calculadora-pis-cofins', 'credit_breakdown')]
    #[CoversPlusFeature('calculadora-pis-cofins', 'comparison')]
    #[CoversPlusFeature('calculadora-pis-cofins', 'export')]
    public function test_non_cumulative_comparison_and_plus_operations_are_rendered(): void
    {
        $payload = $this->payload();
        $payload['regime'] = 'non_cumulative';
        $payload['taxable_revenue'] = '10.000,00';
        $payload['credit_base'] = '4.000,00';
        $payload['compare_regimes'] = '1';
        $payload['operations'] = [['description' => 'Operação adicional', 'revenue' => '5.000,00', 'credit_base' => '1.000,00']];

        $this->post(route('tools.calculadora-pis-cofins.calculate'), $payload)
            ->assertOk()
            ->assertSee('Comparação cumulativo')
            ->assertSee('Operação adicional')
            ->assertSee('R$ 925,00')
            ->assertSee('Exportar PDF')
            ->assertSee('Baixar Excel');
    }

    private function payload(): array
    {
        return [
            'period' => '2026-08',
            'regime' => 'cumulative',
            'taxable_revenue' => '0',
            'credit_base' => '0',
            'pis_withheld' => '0',
            'cofins_withheld' => '0',
            'confirm_scope' => '1',
        ];
    }
}
