<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.new-hire-cost-calculator.index'))
            ->assertOk()
            ->assertSee('New Hire Cost Calculator');
    }
}
