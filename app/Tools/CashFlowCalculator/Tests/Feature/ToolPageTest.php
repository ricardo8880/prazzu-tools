<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.fluxo-de-caixa.index'))
            ->assertOk()
            ->assertSee('Calculadora de Fluxo de Caixa');
    }
}
