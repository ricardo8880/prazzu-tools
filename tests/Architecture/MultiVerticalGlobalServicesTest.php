<?php

use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Navigation\Application\VerticalBreadcrumbContext;
use App\Core\Seo\Application\VerticalSeoContext;

it('keeps global services shared while carrying vertical context', function (): void {
    $analytics = file_get_contents(app_path('Core/Analytics/Infrastructure/Persistence/EloquentAnalyticsEventRepository.php'));
    $toolSitemap = file_get_contents(app_path('Http/Controllers/Seo/ToolSitemapController.php'));
    $blogSitemap = file_get_contents(app_path('Http/Controllers/Seo/BlogSitemapController.php'));

    expect($analytics)->toContain("'vertical_slug' => \$context->verticalSlug")
        ->and($toolSitemap)->toContain('ToolCatalog')
        ->and($blogSitemap)->toContain('VerticalContext')
        ->and(class_exists(VerticalSeoContext::class))->toBeTrue()
        ->and(class_exists(VerticalBreadcrumbContext::class))->toBeTrue()
        ->and((new ReflectionClass(AnalyticsContext::class))->hasProperty('verticalSlug'))->toBeTrue();
});

it('does not create per-vertical global service classes', function (): void {
    $paths = collect(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path())))
        ->filter(fn (SplFileInfo $file): bool => $file->isFile() && $file->getExtension() === 'php')
        ->map(fn (SplFileInfo $file): string => $file->getFilename());

    foreach (['AnalyticsContabilidade', 'AnalyticsRH', 'SeoContabilidade', 'SeoRH', 'AdminContabilidade', 'AdminRH'] as $forbidden) {
        expect($paths->contains(fn (string $name): bool => str_contains($name, $forbidden)))->toBeFalse();
    }
});

it('provides analytics tool context to the shared layout without relying on a blade-local variable', function (): void {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

    expect($provider)->toContain("View::composer('layouts.app'")
        ->and($provider)->toContain("\$view->with('analyticsToolSlug', \$analyticsToolSlug)")
        ->and($layout)->toContain('@if(! empty($analyticsToolSlug))')
        ->and($layout)->not->toContain('$analyticsRouteName = request()->route()?->getName()');
});
