<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('salary-increase-calculator');

        self::assertNotNull($module);
        self::assertSame('salary-increase-calculator', $module->manifest()->slug);
    }
}
