<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_tool_is_registered_in_the_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('emissor-de-recibos');

        self::assertNotNull($module);
        self::assertSame('emissor-de-recibos', $module->manifest()->slug);
    }
}
