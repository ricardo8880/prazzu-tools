<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Contracts\VerticalRegistry;
use Tests\TestCase;

final class MultiVerticalExpansionProofTest extends TestCase
{
    public function test_rh_context_filters_catalog_and_uses_shared_home_and_seo(): void
    {
        $vertical = $this->app->make(VerticalRegistry::class)->find('rh');
        self::assertNotNull($vertical);
        $this->app->make(VerticalContext::class)->activate($vertical);

        $catalog = $this->app->make(ToolCatalog::class);
        self::assertSame(['calculadora-turnover'], $catalog->all()->pluck('slug')->all());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('gestão de pessoas')
            ->assertSee('Calculadora de Turnover')
            ->assertDontSee('Calculadora DIFAL');

        $this->get(route('tools.index'))
            ->assertOk()
            ->assertSee('Calculadora de Turnover')
            ->assertDontSee('Calculadora de Simples Nacional');
    }
}
