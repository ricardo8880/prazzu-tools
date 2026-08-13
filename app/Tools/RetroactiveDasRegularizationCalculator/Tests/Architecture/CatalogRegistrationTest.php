<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('calculadora-das-retroativo-regularizacao-simples');
        self::assertNotNull($module);
        self::assertSame('calculadora-das-retroativo-regularizacao-simples', $module->manifest()->slug);
    }
}
