<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('simulador-distribuicao-lucros-balanco');
        self::assertNotNull($module);
        self::assertSame('simulador-distribuicao-lucros-balanco', $module->manifest()->slug);
    }
}
