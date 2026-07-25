<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.capital-de-giro.index'))
            ->assertOk()
            ->assertSee('Calculadora de Capital de Giro');
    }
}
