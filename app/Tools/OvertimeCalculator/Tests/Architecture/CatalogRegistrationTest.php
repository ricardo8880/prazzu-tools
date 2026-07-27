<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Tests\Architecture;
use App\Core\Tools\ToolRegistry; use Tests\TestCase;
final class CatalogRegistrationTest extends TestCase { public function test_tool_is_registered(): void { $m=app(ToolRegistry::class)->findModule('calculadora-hora-extra'); self::assertNotNull($m); } }
