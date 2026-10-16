<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_public_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('contract-generator');

        self::assertNotNull($module);
        self::assertSame('contract-generator', $module->manifest()->slug);
        self::assertContains('contract-generator', collect(app(ToolRegistry::class)->manifests())->pluck('slug')->all());
    }
}
