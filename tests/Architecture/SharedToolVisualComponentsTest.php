<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SharedToolVisualComponentsTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function componentFiles(): array
    {
        return [
            'tool page' => ['resources/views/components/tools/page.blade.php'],
            'integration import' => ['resources/views/components/tools/integration-import.blade.php'],
            'generic input' => ['resources/views/components/tools/form/input.blade.php'],
            'money input' => ['resources/views/components/tools/form/money.blade.php'],
            'select input' => ['resources/views/components/tools/form/select.blade.php'],
            'switch input' => ['resources/views/components/tools/form/switch.blade.php'],
            'result metric' => ['resources/views/components/tools/result-metric.blade.php'],
        ];
    }

    #[DataProvider('componentFiles')]
    public function test_shared_visual_component_exists(string $path): void
    {
        self::assertFileExists(base_path($path));
    }


    public function test_tool_page_wrapper_does_not_repeat_feature_tiers_in_tool_views(): void
    {
        $views = glob(base_path('app/Tools/*/Resources/views/index.blade.php'));

        self::assertIsArray($views);

        foreach ($views as $viewPath) {
            $view = file_get_contents($viewPath);

            self::assertIsString($view);

            if (! str_contains($view, '<x-tools.page')) {
                continue;
            }

            self::assertStringNotContainsString(
                '<x-tool-feature-tiers',
                $view,
                sprintf(
                    '%s usa <x-tools.page>, que já renderiza os tiers; a chamada manual duplica o conteúdo.',
                    str_replace(base_path().DIRECTORY_SEPARATOR, '', $viewPath),
                ),
            );
        }
    }

    public function test_simples_nacional_is_the_visual_standard_pilot(): void
    {
        $view = file_get_contents(base_path('app/Tools/SimplesNacionalCalculator/Resources/views/index.blade.php'));

        self::assertIsString($view);
        self::assertStringContainsString('<x-tools.page', $view);
        self::assertStringContainsString('<x-tools.form.money', $view);
        self::assertStringContainsString('<x-tools.integration-import', $view);
        self::assertStringContainsString('<x-tools.result-metric', $view);
    }
}
