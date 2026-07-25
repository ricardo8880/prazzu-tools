<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.simulador-fator-r.index'))
            ->assertOk()
            ->assertSee('Simulador de Fator R');
    }
}
