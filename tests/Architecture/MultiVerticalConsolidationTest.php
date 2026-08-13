<?php

namespace Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use App\Core\Verticals\Contracts\VerticalRegistry;
use Tests\TestCase;

final class MultiVerticalConsolidationTest extends TestCase
{
    public function test_current_inventory_is_consistent_with_registered_verticals(): void
    {
        $registered = collect(app(VerticalRegistry::class)->all())->pluck('slug')->all();
        $inventory = collect(config('product_tools.official', []));

        self::assertCount(43, $inventory);
        self::assertCount(42, $inventory->where('vertical', 'contabilidade'));
        self::assertCount(1, $inventory->where('vertical', 'rh'));
        self::assertEmpty($inventory->pluck('vertical')->diff($registered));

        $manifests = collect(app(ToolRegistry::class)->manifests())->keyBy('slug');
        foreach ($inventory as $tool) {
            self::assertSame($tool['vertical'], $manifests->get($tool['slug'])?->vertical, $tool['slug']);
        }
    }

    public function test_shared_public_surfaces_do_not_define_prazzu_as_accounting_only(): void
    {
        $files = [
            resource_path('views/pages/about.blade.php'),
            resource_path('views/pages/tools/index.blade.php'),
            resource_path('views/pages/resources/index.blade.php'),
            resource_path('views/blog/index.blade.php'),
            config_path('platform.php'),
        ];

        $forbidden = [
            'Plataforma de ferramentas contábeis',
            'Foco contábil',
            'Construída para evoluir junto com a contabilidade',
            'Blog de contabilidade — Prazzu Tools',
            'simplificar a rotina contábil',
        ];

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            self::assertNotFalse($contents);

            foreach ($forbidden as $phrase) {
                self::assertStringNotContainsString($phrase, $contents, $file);
            }
        }
    }

    public function test_blog_admin_keeps_content_relationships_inside_the_post_vertical(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/Blog/BlogPostController.php'));
        self::assertNotFalse($controller);

        self::assertStringContainsString('->forVertical($post->vertical_slug)', $controller);
        self::assertStringContainsString("->where('vertical_slug', \$selectedVertical)", $controller);
        self::assertStringContainsString('$tool->vertical === $selectedVertical', $controller);
        self::assertStringContainsString('Selecione apenas ferramentas pertencentes à mesma vertical da postagem.', $controller);
        self::assertStringNotContainsString("'selectedVertical' => \$vertical", $controller);
    }

    public function test_vertical_specific_infrastructure_was_not_duplicated(): void
    {
        foreach ([
            app_path('Http/Controllers/Platform/HomeContabilidadeController.php'),
            app_path('Http/Controllers/Platform/HomeRhController.php'),
            app_path('Core/Analytics/AnalyticsContabilidade.php'),
            app_path('Core/Analytics/AnalyticsRh.php'),
            app_path('Core/Seo/SeoContabilidade.php'),
            app_path('Core/Seo/SeoRh.php'),
        ] as $path) {
            self::assertFileDoesNotExist($path);
        }
    }
}
