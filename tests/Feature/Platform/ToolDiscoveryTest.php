<?php

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use Tests\TestCase;

final class ToolDiscoveryTest extends TestCase
{
    public function test_tool_sitemap_uses_the_visible_catalog_and_populated_categories(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $tools = $catalog->all();
        $categories = $catalog->categories(false);

        $response = $this->get(route('tools.sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee(route('tools.index'), false);

        foreach ($categories as $category) {
            $this->assertGreaterThan(0, $category['count']);
            $response->assertSee($category['url'], false);
        }

        foreach ($tools as $tool) {
            $response->assertSee(route($tool['route_name']), false);
        }

        $this->assertSame(
            1 + $categories->count() + $tools->count(),
            substr_count((string) $response->getContent(), '<url>'),
        );
    }

    public function test_catalog_projects_and_searches_the_public_category_metadata(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $tool = $catalog->all()->firstWhere('category', 'fiscal');

        $this->assertIsArray($tool);
        $this->assertSame(config('tools.categories.fiscal.name'), $tool['category_name']);
        $this->assertSame(config('tools.categories.fiscal.icon'), $tool['category_icon']);
        $this->assertEqualsCanonicalizing(
            $catalog->byCategory('fiscal')->pluck('slug')->all(),
            $catalog->search(config('tools.categories.fiscal.name'))->pluck('slug')->all(),
        );

        $this->get(route('tools.index'))
            ->assertOk()
            ->assertSee($tool['category_name']);
    }

    public function test_home_exposes_the_real_tool_counts_by_category(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $counts = $catalog->categories()
            ->mapWithKeys(static fn (array $category): array => [$category['slug'] => $category['count']])
            ->all();

        $this->assertSame([
            'todas' => 36,
            'geradores' => 6,
            'calculadoras' => 6,
            'validadores' => 1,
            'fiscal' => 13,
            'trabalhista' => 10,
        ], $counts);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Geradores')
            ->assertSee('Fiscal e tributário')
            ->assertDontSee('Documentos');
    }

    public function test_public_categories_exclude_empty_taxonomy_entries(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $categories = $catalog->categories(false);

        $this->assertNotEmpty($categories);
        $this->assertNotContains(0, $categories->pluck('count')->all());
        $this->assertEqualsCanonicalizing(
            $catalog->all()->pluck('category')->unique()->values()->all(),
            $categories->pluck('slug')->all(),
        );
    }

    public function test_robots_file_references_the_tool_sitemap_without_removing_crawl_rules(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertIsString($robots);
        $this->assertStringContainsString('User-agent: *', $robots);
        $this->assertStringContainsString('Disallow:', $robots);
        $this->assertStringContainsString('/sitemap-tools.xml', $robots);
    }
}
