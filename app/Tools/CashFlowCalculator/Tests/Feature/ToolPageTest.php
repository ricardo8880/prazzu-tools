<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.cash-flow-calculator.index'))
            ->assertOk()
            ->assertSee('Cash Flow Calculator');
    }
}
