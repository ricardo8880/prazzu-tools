<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.comissao-vendedores.index'))
            ->assertOk()
            ->assertSee('Calculadora de Comissão de Vendedores');
    }
}
