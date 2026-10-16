<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.pay-stub-generator.index'))
            ->assertOk()
            ->assertSee('Gerador de Holerite');
    }
}
