<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('calculadora-salario-liquido');

        self::assertNotNull($module);
        self::assertSame('calculadora-salario-liquido', $module->manifest()->slug);
    }
}
