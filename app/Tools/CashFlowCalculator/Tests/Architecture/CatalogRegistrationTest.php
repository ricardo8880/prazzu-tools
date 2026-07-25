<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('fluxo-de-caixa');

        self::assertNotNull($module);
        self::assertSame('fluxo-de-caixa', $module->manifest()->slug);
    }
}
