<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_public_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('gerador-de-contratos');

        self::assertNotNull($module);
        self::assertSame('gerador-de-contratos', $module->manifest()->slug);
        self::assertContains('gerador-de-contratos', collect(app(ToolRegistry::class)->manifests())->pluck('slug')->all());
    }
}
