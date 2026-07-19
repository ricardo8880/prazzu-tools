<?php

namespace Tests\Architecture;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Core\Tools\Support\ToolModuleValidator;
use App\Tools\ArchitectureGeneratorProbe\Tool;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
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
            'application action' => ['Action.stub'],
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

    public function test_generated_documentation_contract_contains_every_required_section(): void
    {
        $contents = file_get_contents(base_path('stubs/tool/README.stub'));

        self::assertIsString($contents);

        foreach (['Descrição', 'Funcionalidades', 'Experiência Essencial', 'Prazzu Plus', 'Regras de domínio', 'Dependências', 'Histórico de versões'] as $section) {
            self::assertStringContainsString("## {$section}", $contents);
        }
    }

    public function test_generator_rejects_a_new_tool_outside_draft_status(): void
    {
        $modulePath = app_path('Tools/ArchitectureStatusProbe');

        self::assertDirectoryDoesNotExist($modulePath);

        $exitCode = Artisan::call('make:tool', [
            'name' => 'ArchitectureStatusProbe',
            '--status' => 'active',
        ]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertDirectoryDoesNotExist($modulePath);
        self::assertStringContainsString('deve iniciar com o estado [draft]', Artisan::output());
    }

    public function test_generator_creates_a_loadable_and_valid_draft_module(): void
    {
        $files = new Filesystem;
        $modulePath = app_path('Tools/ArchitectureGeneratorProbe');
        $configurationPath = config_path('tools/modules.php');
        $originalConfiguration = $files->get($configurationPath);

        self::assertDirectoryDoesNotExist($modulePath);

        try {
            $exitCode = Artisan::call('make:tool', [
                'name' => 'ArchitectureGeneratorProbe',
                '--slug' => 'architecture-generator-probe',
                '--category' => 'fiscal',
            ]);

            self::assertSame(Command::SUCCESS, $exitCode, Artisan::output());
            self::assertFileExists($modulePath.'/Application/Actions/ShowToolPage.php');
            self::assertFileExists($modulePath.'/Tests/Unit/ToolManifestTest.php');
            self::assertStringContainsString(
                '\\App\\Tools\\ArchitectureGeneratorProbe\\Tool::class,',
                $files->get($configurationPath),
            );

            $module = new Tool;
            self::assertSame(ToolStatus::Draft, $module->manifest()->status);
            self::assertNotEmpty($module->manifest()->featuresFor(ToolFeatureTier::Essential));
            self::assertNotEmpty($module->manifest()->featuresFor(ToolFeatureTier::Plus));
            (new ToolModuleValidator)->validate($module);
        } finally {
            $files->replace($configurationPath, $originalConfiguration);
            $files->deleteDirectory($modulePath);
        }
    }
}
