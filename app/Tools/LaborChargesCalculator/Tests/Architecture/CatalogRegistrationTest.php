<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('employer-payroll-tax-calculator');

        self::assertNotNull($module);
        self::assertSame('employer-payroll-tax-calculator', $module->manifest()->slug);
    }
}
