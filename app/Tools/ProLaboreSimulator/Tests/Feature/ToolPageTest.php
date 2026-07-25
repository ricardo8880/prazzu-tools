<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available_from_the_public_catalog(): void
    {
        $this->get(route('tools.simulador-pro-labore-ideal.index'))
            ->assertOk()
            ->assertSee('Simulador de Pró-Labore Ideal');
    }
}
