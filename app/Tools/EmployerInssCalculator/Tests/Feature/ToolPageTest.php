<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.inss-patronal.index'))
            ->assertOk()
            ->assertSee('Calculadora de INSS Patronal');
    }
}
