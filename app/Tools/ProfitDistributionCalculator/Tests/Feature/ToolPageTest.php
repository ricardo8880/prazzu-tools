<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available_from_the_public_catalog(): void
    {
        $this->get(route('tools.owner-distribution-calculator.index'))
            ->assertOk()
            ->assertSee('Owner Distribution Calculator');
    }
}
