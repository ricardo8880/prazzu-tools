<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class CatalogRegistrationTest extends TestCase
{
    public function test_module_is_registered_in_fiscal_catalog(): void
    {
        $module = app(ToolRegistry::class)->findModule('gerador-darf-gps');

        self::assertNotNull($module);
        self::assertSame('gerador-darf-gps', $module->manifest()->slug);
    }
}
