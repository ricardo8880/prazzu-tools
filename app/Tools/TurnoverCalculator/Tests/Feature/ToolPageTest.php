<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_and_calculation_route_are_available(): void
    {
        $this->get(route('tools.calculadora-turnover.index'))
            ->assertOk()
            ->assertSee('Calculadora de Turnover');

        $this->post(route('tools.calculadora-turnover.calculate'), [
            'admissions' => 10,
            'terminations' => 6,
            'average_headcount' => 80,
        ])->assertOk()->assertSee('10,00%');
    }
}
