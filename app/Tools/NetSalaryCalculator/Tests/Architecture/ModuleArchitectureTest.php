<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Architecture;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ModuleArchitectureTest extends TestCase
{
    public function test_module_does_not_import_internal_classes_from_other_tools(): void
    {
        $root = dirname(__DIR__, 2);
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            self::assertNotFalse($contents);
            self::assertDoesNotMatchRegularExpression('/use App\\\\Tools\\\\(?!NetSalaryCalculator\\\\)/', $contents);
        }
    }
}
