<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('take-home-pay-calculator');

        self::assertNotNull($module);
        self::assertSame('take-home-pay-calculator', $module->manifest()->slug);
    }
}
