<?php

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class OrganizationArchitectureTest extends TestCase
{
    #[DataProvider('toolPhpFiles')]
    public function test_tools_do_not_depend_on_enterprise_implementation(string $file): void
    {
        $contents = file_get_contents($file);

        self::assertIsString($contents);
        self::assertStringNotContainsString('App\\Models\\Organization', $contents, $file);
        self::assertStringNotContainsString('App\\Models\\OrganizationMember', $contents, $file);
        self::assertStringNotContainsString('App\\Models\\OrganizationSubscription', $contents, $file);
        self::assertStringNotContainsString('App\\Models\\OrganizationSeat', $contents, $file);
        self::assertStringNotContainsString('App\\Core\\Organizations', $contents, $file);
    }

    public function test_enterprise_domain_remains_inside_core_and_shared_application_layers(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        self::assertDirectoryExists($projectRoot.'/app/Core/Organizations');
        self::assertDirectoryDoesNotExist($projectRoot.'/app/Tools/Organizations');
    }

    public static function toolPhpFiles(): iterable
    {
        $directory = dirname(__DIR__, 2).'/app/Tools';

        if (! is_dir($directory)) {
            return;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                yield $file->getPathname() => [$file->getPathname()];
            }
        }
    }
}
