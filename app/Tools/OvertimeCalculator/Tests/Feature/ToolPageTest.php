<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Tests\Feature;
use Tests\TestCase;
final class ToolPageTest extends TestCase { public function test_page_is_public(): void { $this->get('/ferramentas/calculadora-hora-extra')->assertOk()->assertSee('Hora Extra'); } }
