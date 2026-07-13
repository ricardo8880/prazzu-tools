<?php

namespace Tests\Feature\Platform;

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

        foreach ($tools as $tool) {
            $this->assertContains($tool['category'], $categories);
            $this->assertNotEmpty($tool['name']);
            $this->assertNotEmpty($tool['description']);
            $this->assertNotEmpty($tool['icon']);
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
