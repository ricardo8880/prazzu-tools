<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_public_and_indexable(): void
    {
        $this->get(route('tools.calculadora-salario-liquido.index'))
            ->assertOk()
            ->assertSee('Calculadora de Salário Líquido')
            ->assertSee('INSS')
            ->assertSee('IRRF');
    }

    public function test_public_user_can_calculate_without_login(): void
    {
        $this->post(route('tools.calculadora-salario-liquido.calculate'), [
            'competence' => '2026-01',
            'base_salary' => '5000,00',
            'dependents' => 0,
            'confirm_assumptions' => '1',
        ])->assertOk()
            ->assertSee('R$ 4.498,49')
            ->assertSee('Salário líquido');
    }
}
