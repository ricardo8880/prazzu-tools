<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class HistoryContinuityContextExperienceTest extends TestCase
{
    public function test_context_contract_keeps_domain_meaning_out_of_the_core_resolver(): void
    {
        $resolver = file_get_contents(base_path('app/Core/Tools/History/Services/ToolHistoryContextResolver.php'));

        self::assertIsString($resolver);
        self::assertStringNotContainsString('calculadora-salario-liquido', $resolver);
        self::assertStringNotContainsString('calculadora-simples-nacional', $resolver);
        self::assertStringContainsString('ProvidesHistoryContext', $resolver);
    }

    public function test_continuity_surfaces_render_the_optional_context_label(): void
    {
        foreach ([
            'resources/views/welcome.blade.php',
            'resources/views/account/show.blade.php',
        ] as $path) {
            $view = file_get_contents(base_path($path));
            self::assertIsString($view);
            self::assertStringContainsString("['context_label']", $view);
        }

        $historyIndex = file_get_contents(base_path('resources/views/tools/shared/history/index.blade.php'));
        $historyShow = file_get_contents(base_path('resources/views/tools/shared/history/show.blade.php'));
        self::assertIsString($historyIndex);
        self::assertIsString($historyShow);
        self::assertStringContainsString('$historyContexts', $historyIndex);
        self::assertStringContainsString('$contextLabel', $historyShow);
    }
}
