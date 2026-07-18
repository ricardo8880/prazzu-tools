<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_page_is_available_with_free_calculation_form(): void
    {
        $this->get(route('tools.calculadora-simples-nacional.index'))
            ->assertOk()
            ->assertSee('Calculadora de Simples Nacional')
            ->assertSee('Calcular DAS')
            ->assertSee('Fator R');
    }

    public function test_calculates_selected_annex_and_returns_complete_result(): void
    {
        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.calculate'), [
                'annex' => 'I',
                'rbt12' => '180.000,00',
                'monthly_revenue' => '15.000,00',
            ])
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('calculation_result', static function (array $result): bool {
                return $result['annex'] === 'I'
                    && $result['bracket'] === 1
                    && $result['effective_rate'] === '4%'
                    && $result['estimated_das'] === 'R$ 600,00';
            });
    }

    public function test_factor_r_selects_annex_iii_and_calculates_das(): void
    {
        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.calculate'), [
                'use_factor_r' => '1',
                'rbt12' => '180.000,00',
                'monthly_revenue' => '15.000,00',
                'payroll_12' => '50.400,00',
            ])
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('factor_r_result', static fn (array $result): bool => $result['applicable_annex'] === 'III')
            ->assertSessionHas('calculation_result', static fn (array $result): bool => $result['annex'] === 'III');
    }

    public function test_factor_r_requires_payroll(): void
    {
        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.calculate'), [
                'use_factor_r' => '1',
                'rbt12' => '180.000,00',
                'monthly_revenue' => '15.000,00',
            ])
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHasErrors('payroll_12');
    }
}
