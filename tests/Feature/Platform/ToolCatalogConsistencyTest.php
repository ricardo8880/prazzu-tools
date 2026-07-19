<?php

namespace Tests\Feature\Platform;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class ToolCatalogConsistencyTest extends TestCase
{
    public function test_catalog_slugs_are_unique_and_categories_exist(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $tools = $catalog->all();
        $categories = array_keys(config('tools.categories', []));

        $this->assertSame($tools->count(), $tools->pluck('slug')->unique()->count());
        $this->assertEqualsCanonicalizing(
            array_map(static fn (ToolCategory $category): string => $category->value, ToolCategory::cases()),
            $categories,
        );

        foreach ($tools as $tool) {
            $this->assertContains($tool['category'], $categories);
            $this->assertNotEmpty($tool['name']);
            $this->assertNotEmpty($tool['description']);
            $this->assertNotEmpty($tool['icon']);
            $this->assertNotEmpty($tool['version']);
            $this->assertStringStartsWith('tools.', $tool['route_name']);
        }
    }

    public function test_catalog_contains_only_visible_registered_tool_manifests(): void
    {
        $catalogSlugs = $this->app->make(ToolCatalog::class)->all()->pluck('slug')->sort()->values();
        $manifestSlugs = collect($this->app->make(ToolRegistry::class)->manifests())
            ->pluck('slug')
            ->sort()
            ->values();

        $this->assertSame($manifestSlugs->all(), $catalogSlugs->all());
        $this->assertSame([], config('tools.catalog', []));
        $this->assertSame([], config('tools.metrics', []));

        foreach ($this->app->make(ToolCatalog::class)->all() as $tool) {
            $this->assertNotSame('0.0.0-placeholder', $tool['version']);
            $this->assertArrayNotHasKey('uses_count', $tool);
            $this->assertArrayNotHasKey('uses_label', $tool);
            $this->assertArrayNotHasKey('is_popular', $tool);
            $this->assertArrayNotHasKey('is_premium', $tool);
            $this->assertNotEmpty($tool['essential_features']);
            $this->assertNotEmpty($tool['plus_features']);
            $this->assertTrue($tool['has_plus_features']);
            $this->assertSame('Grátis + Plus', $tool['badge']);
            $this->assertNotSame('tools.show', $tool['route_name']);
            $this->assertTrue($this->app['router']->has($tool['route_name']));
        }

        $this->get('/ferramentas/ferramenta-inexistente')->assertNotFound();
    }

    public function test_home_and_catalog_use_the_same_tool_source(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);

        $this->get('/')
            ->assertOk()
            ->assertSee($catalog->featured()->first()['name']);

        $this->get('/ferramentas')
            ->assertOk()
            ->assertSee($catalog->all()->first()['name']);
    }
}
