<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ModuleArchitectureTest extends TestCase
{
    public function test_module_does_not_import_other_tool_domains(): void
    {
        $root = dirname(__DIR__, 2);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            self::assertIsString($contents);
            self::assertDoesNotMatchRegularExpression('/App\\\\Tools\\\\(?!TurnoverCalculator)/', $contents);
        }
    }
}
