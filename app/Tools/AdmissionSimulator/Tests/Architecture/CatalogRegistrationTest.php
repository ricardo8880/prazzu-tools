<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('simulador-admissao');

        self::assertNotNull($module);
        self::assertSame('simulador-admissao', $module->manifest()->slug);
    }
}
