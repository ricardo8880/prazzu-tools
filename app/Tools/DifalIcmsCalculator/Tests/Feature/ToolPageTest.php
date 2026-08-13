<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_public(): void
    {
        $this->get('/ferramentas/calculadora-difal-icms')->assertOk()->assertSee('DIFAL');
    }
}
