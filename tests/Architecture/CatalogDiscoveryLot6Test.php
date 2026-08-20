<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class CatalogDiscoveryLot6Test extends TestCase
{
    public function test_catalog_discovery_reuses_existing_continuity_favorites_and_editorial_sources(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Platform/ToolCatalogController.php'));
        $view = file_get_contents(resource_path('views/pages/tools/index.blade.php'));

        self::assertIsString($controller);
        self::assertIsString($view);
        self::assertStringContainsString('UserToolFavorites', $controller);
        self::assertStringContainsString('UserToolContinuityQuery', $controller);
        self::assertStringContainsString('$this->catalog->featured()', $controller);
        self::assertStringContainsString("'recentSlugs' => \$recentToolSlugs", $view);
        self::assertStringContainsString("'featuredSlugs' => \$featuredToolSlugs", $view);
        self::assertStringNotContainsString('App\\Tools\\', $controller);
    }
}
