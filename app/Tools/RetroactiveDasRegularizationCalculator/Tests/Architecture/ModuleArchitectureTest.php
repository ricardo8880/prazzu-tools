<?php

declare(strict_types=1);
namespace App\Tools\RetroactiveDasRegularizationCalculator\Tests\Architecture;
use PHPUnit\Framework\TestCase;
final class ModuleArchitectureTest extends TestCase
{
    public function test_module_does_not_import_other_tool_domains(): void
    {
        $files=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__DIR__,2)));
        foreach($files as $file){if(!$file->isFile()||$file->getExtension()!=='php')continue;$source=file_get_contents($file->getPathname());self::assertDoesNotMatchRegularExpression('/use App\\Tools\\(?!RetroactiveDasRegularizationCalculator)/',$source,$file->getPathname());}
    }
}
