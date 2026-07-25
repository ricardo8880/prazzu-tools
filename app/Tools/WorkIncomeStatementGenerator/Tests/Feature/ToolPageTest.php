<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.declaracao-trabalho-renda.index'))
            ->assertOk()
            ->assertSee('Gerador de Declaração de Trabalho/Renda');
    }
}
