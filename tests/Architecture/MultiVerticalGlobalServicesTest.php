<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Navigation\Application\VerticalBreadcrumbContext;
use App\Core\Seo\Application\VerticalSeoContext;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Tests\TestCase;

final class MultiVerticalGlobalServicesTest extends TestCase
{
    public function test_global_services_remain_shared_while_carrying_vertical_context(): void
    {
        $analytics = file_get_contents(app_path('Core/Analytics/Infrastructure/Persistence/EloquentAnalyticsEventRepository.php'));
        $toolSitemap = file_get_contents(app_path('Http/Controllers/Seo/ToolSitemapController.php'));
        $blogSitemap = file_get_contents(app_path('Http/Controllers/Seo/BlogSitemapController.php'));

        self::assertIsString($analytics);
        self::assertIsString($toolSitemap);
        self::assertIsString($blogSitemap);

        self::assertStringContainsString("'vertical_slug' => \$context->verticalSlug", $analytics);
        self::assertStringContainsString('ToolCatalog', $toolSitemap);
        self::assertStringContainsString('VerticalContext', $blogSitemap);
        self::assertTrue(class_exists(VerticalSeoContext::class));
        self::assertTrue(class_exists(VerticalBreadcrumbContext::class));
        self::assertTrue((new ReflectionClass(AnalyticsContext::class))->hasProperty('verticalSlug'));
    }

    public function test_per_vertical_global_service_classes_do_not_exist(): void
    {
        $paths = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path()));

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $paths[] = $file->getFilename();
        }

        foreach (['AnalyticsContabilidade', 'AnalyticsRH', 'SeoContabilidade', 'SeoRH', 'AdminContabilidade', 'AdminRH'] as $forbidden) {
            self::assertFalse(
                collect($paths)->contains(
                    fn (string $name): bool => str_contains($name, $forbidden)
                ),
                "Foi encontrada infraestrutura específica por vertical contendo [{$forbidden}]."
            );
        }
    }

    public function test_shared_layout_receives_analytics_tool_context_without_blade_local_state(): void
    {
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        self::assertIsString($provider);
        self::assertIsString($layout);

        self::assertStringContainsString("View::composer('layouts.app'", $provider);
        self::assertStringContainsString("\$view->with('analyticsToolSlug', \$analyticsToolSlug)", $provider);
        self::assertStringContainsString('@if(! empty($analyticsToolSlug))', $layout);
        self::assertStringNotContainsString('$analyticsRouteName = request()->route()?->getName()', $layout);
    }
}
