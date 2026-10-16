<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.working-capital-calculator.index'))
            ->assertOk()
            ->assertSee('Working Capital Calculator');
    }
}
