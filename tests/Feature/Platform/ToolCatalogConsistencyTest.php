<?php

namespace Tests\Feature\Platform;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\ToolCatalog;
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

    public function test_static_metadata_and_demonstration_metrics_are_separated(): void
    {
        foreach (config('tools.catalog', []) as $tool) {
            $this->assertArrayNotHasKey('uses_count', $tool);
            $this->assertArrayNotHasKey('is_popular', $tool);
            $this->assertArrayHasKey($tool['slug'], config('tools.metrics', []));
        }
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
