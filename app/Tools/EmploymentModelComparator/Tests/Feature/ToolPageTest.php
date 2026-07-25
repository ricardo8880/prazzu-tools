<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.comparador-clt-pj-autonomo.index'))
            ->assertOk()
            ->assertSee('Simulador CLT × PJ × Autônomo');
    }
}
