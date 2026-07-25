<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ModuleArchitectureTest extends TestCase
{
    public function test_module_does_not_import_another_tool_domain(): void
    {
        $files = glob(dirname(__DIR__, 2).'/**/*.php', GLOB_BRACE) ?: [];
        foreach ($files as $file) {
            self::assertStringNotContainsString('App\\Tools\\ProfitDistributionCalculator', file_get_contents($file));
        }
    }
}
