<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-irpj-csll-lucro-presumido.index'))
            ->assertOk()
            ->assertSee('IRPJ e CSLL')
            ->assertSee('Lucro Presumido')
            ->assertSee('2026');
    }

    public function test_calculation_validates_that_at_least_one_activity_has_revenue(): void
    {
        $this->post(route('tools.calculadora-irpj-csll-lucro-presumido.calculate'), $this->payload())
            ->assertSessionHasErrors('commerce_revenue');
    }

    public function test_calculation_returns_result_for_a_valid_first_quarter_scenario(): void
    {
        $payload = $this->payload();
        $payload['commerce_revenue'] = '1.000.000,00';

        $this->post(route('tools.calculadora-irpj-csll-lucro-presumido.calculate'), $payload)
            ->assertOk()
            ->assertSee('R$ 24.800,00');
    }

    private function payload(): array
    {
        return [
            'quarter' => 1,
            'commerce_revenue' => '0',
            'fuel_revenue' => '0',
            'passenger_transport_revenue' => '0',
            'services_revenue' => '0',
            'other_taxable_additions' => '0',
            'prior_irpj_presumption_revenue' => '0',
            'prior_csll_presumption_revenue' => '0',
            'irpj_credits' => '0',
            'csll_credits' => '0',
            'confirm_scope' => '1',
        ];
    }
}
