<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.ponto-de-equilibrio.index'))
            ->assertOk()
            ->assertSee('Calculadora de Ponto de Equilíbrio');
    }
}
