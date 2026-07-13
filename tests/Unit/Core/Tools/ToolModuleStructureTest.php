<?php

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use Illuminate\Support\Arr;
use Tests\TestCase;

final class ToolModuleStructureTest extends TestCase
{
    public function test_all_configured_modules_follow_the_official_namespace(): void
    {
        $classes = array_values(Arr::flatten(config('tools.modules', [])));

        foreach ($classes as $class) {
            $this->assertIsString($class);
            $this->assertStringStartsWith('App\\Tools\\', $class);
            $this->assertTrue(class_exists($class), "A classe configurada [{$class}] não existe.");
            $this->assertTrue(is_subclass_of($class, ToolModule::class));
        }
    }

    public function test_module_registration_file_keeps_all_generator_markers(): void
    {
        $contents = file_get_contents(config_path('tools/modules.php'));

        foreach (['general', 'fiscal', 'labor', 'corporate', 'documents'] as $group) {
            $this->assertStringContainsString("// <tools:{$group}>", $contents);
        }
    }

    public function test_generator_defaults_are_valid_core_values(): void
    {
        $this->assertSame('outros', ToolCategory::Other->value);
        $this->assertSame('free', ToolAccess::Free->value);
        $this->assertSame('draft', ToolStatus::Draft->value);
    }

    public function test_all_generator_stubs_are_available(): void
    {
        foreach ([
            'Tool.stub',
            'Controller.stub',
            'Request.stub',
            'web.stub',
            'view.stub',
            'UnitTest.stub',
            'FeatureTest.stub',
            'README.stub',
        ] as $stub) {
            $this->assertFileExists(base_path("stubs/tool/{$stub}"));
        }
    }
}
