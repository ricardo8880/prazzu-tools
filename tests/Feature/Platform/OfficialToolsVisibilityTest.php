<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use Tests\TestCase;

final class OfficialToolsVisibilityTest extends TestCase
{
    /** @var array<string, string> */
    private const SEPARATED_TOOLS = [
        'simulador-pro-labore-ideal' => 'Simulador de Pró-Labore Ideal',
        'distribuicao-de-lucros' => 'Calculadora de Distribuição de Lucros',
    ];

    public function test_separated_tools_are_visible_in_the_public_catalog(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $response = $this->get(route('tools.index'))->assertOk();

        foreach (self::SEPARATED_TOOLS as $slug => $name) {
            self::assertNotNull($catalog->find($slug));

            $response
                ->assertSee($name)
                ->assertSee('href="'.route("tools.{$slug}.index").'"', false);
        }
    }

    public function test_separated_tools_are_featured_on_the_default_home(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $featuredTools = $response->viewData('featuredTools');
        $featuredSlugs = $featuredTools->pluck('slug')->all();

        foreach (self::SEPARATED_TOOLS as $slug => $name) {
            self::assertContains($slug, $featuredSlugs);
            $response->assertSee($name);
        }
    }
}
