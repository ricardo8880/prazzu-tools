<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('calculadora-pro-labore-distribuicao-lucros');

        self::assertNotNull($module);
        self::assertSame('calculadora-pro-labore-distribuicao-lucros', $module->manifest()->slug);
    }
}
