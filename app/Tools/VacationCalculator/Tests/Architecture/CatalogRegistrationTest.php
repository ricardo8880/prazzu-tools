<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('calculadora-ferias');

        self::assertNotNull($module);
        self::assertSame('calculadora-ferias', $module->manifest()->slug);
    }
}
