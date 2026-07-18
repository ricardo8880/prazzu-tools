<?php

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_navigation_pages_are_available(): void
    {
        foreach (['/', '/ferramentas', '/blog', '/planos', '/recursos', '/sobre', '/entrar', '/criar-conta', '/sugerir-ferramenta'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_mobile_navigation_uses_all_catalog_categories(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        foreach (config('tools.categories', []) as $category) {
            $response->assertSee($category['name']);
        }
    }

    public function test_catalog_can_be_searched_and_filtered(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);

        $searchResponse = $this->get('/ferramentas?q=CNPJ')->assertOk();
        $expectedSearchSlugs = $catalog->search('CNPJ')->pluck('slug')->all();

        $this->assertNotEmpty($expectedSearchSlugs);
        $this->assertSame(
            $expectedSearchSlugs,
            $searchResponse->viewData('tools')->pluck('slug')->all(),
        );

        $categoryResponse = $this->get('/ferramentas/calculadoras')->assertOk();
        $expectedCategorySlugs = $catalog->search(category: 'calculadoras')->pluck('slug')->all();

        $this->assertNotEmpty($expectedCategorySlugs);
        $this->assertSame(
            $expectedCategorySlugs,
            $categoryResponse->viewData('tools')->pluck('slug')->all(),
        );
    }

    public function test_catalog_tool_page_is_available(): void
    {
        $tool = $this->app->make(ToolCatalog::class)->all()->first();

        $this->assertIsArray($tool);

        $this->get('/ferramentas/'.$tool['slug'])
            ->assertOk()
            ->assertSee($tool['name']);
    }

    public function test_newsletter_validates_and_accepts_email(): void
    {
        $this->from('/')
            ->post('/newsletter', ['email' => 'contador@example.com'])
            ->assertRedirect('/')
            ->assertSessionHas('status');
    }
}
