<?php

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use Tests\TestCase;

final class MarginMarkupToolTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index'))->assertOk()->assertSee('Calculadora de Margem e Markup');
    }
}
