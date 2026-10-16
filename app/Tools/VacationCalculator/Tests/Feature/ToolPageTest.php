<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_pto_page_uses_us_policy_inputs(): void
    {
        $this->get(route('tools.pto-calculator.index'))
            ->assertOk()
            ->assertSee('PTO Calculator')
            ->assertSee('Current accrued PTO hours')
            ->assertSee('Planned PTO hours')
            ->assertSee('PTO accrued per pay period')
            ->assertDontSee('constitutional vacation bonus')
            ->assertDontSee('vacation cash-out allowance');
    }
}
