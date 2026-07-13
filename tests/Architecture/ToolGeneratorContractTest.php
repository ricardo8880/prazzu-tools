<?php

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ToolGeneratorContractTest extends TestCase
{
    #[DataProvider('requiredStubProvider')]
    public function test_tool_generator_keeps_all_required_stubs(string $stub): void
    {
        self::assertFileExists(base_path('stubs/tool/'.$stub));
    }

    /** @return array<string, array{string}> */
    public static function requiredStubProvider(): array
    {
        return [
            'module' => ['Tool.stub'],
            'controller' => ['Controller.stub'],
            'request' => ['Request.stub'],
            'routes' => ['web.stub'],
            'view' => ['view.stub'],
            'unit test' => ['UnitTest.stub'],
            'feature test' => ['FeatureTest.stub'],
            'documentation' => ['README.stub'],
        ];
    }

    public function test_every_registration_group_keeps_its_generator_marker(): void
    {
        $contents = file_get_contents(config_path('tools/modules.php'));

        self::assertIsString($contents);

        foreach (['general', 'fiscal', 'labor', 'corporate', 'documents'] as $group) {
            self::assertStringContainsString("// <tools:{$group}>", $contents);
        }
    }
}
