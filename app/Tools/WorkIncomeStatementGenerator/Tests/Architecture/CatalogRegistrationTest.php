<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('declaracao-trabalho-renda');

        self::assertNotNull($module);
        self::assertSame('declaracao-trabalho-renda', $module->manifest()->slug);
    }
}
