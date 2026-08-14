<?php

namespace Tests\Feature\Platform;

use App\Core\Tools\ToolCatalog;
use Tests\TestCase;

final class HomeProblemOrientedExperienceTest extends TestCase
{
    public function test_default_home_leads_with_the_problem_to_solve_without_changing_latest_tools_contract(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $response = $this->get('/')->assertOk();

        $response
            ->assertSee('O que você precisa')
            ->assertSee('Descreva o que você precisa resolver')
            ->assertSee('Explore pelo tipo de tarefa')
            ->assertSee('Calcular rescisão')
            ->assertSee('Validar CNPJ ou CPF');

        self::assertSame(
            $catalog->latest(8)->pluck('slug')->values()->all(),
            $response->viewData('featuredTools')->pluck('slug')->values()->all(),
        );
    }

    public function test_problem_shortcuts_are_real_searches_with_at_least_one_catalog_match(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $shortcuts = config('home.verticals.contabilidade.hero.problem_shortcuts', []);

        self::assertNotEmpty($shortcuts);

        foreach ($shortcuts as $shortcut) {
            self::assertIsArray($shortcut);
            self::assertNotEmpty($shortcut['label'] ?? null);
            self::assertNotEmpty($shortcut['query'] ?? null);
            self::assertGreaterThan(
                0,
                $catalog->search((string) $shortcut['query'])->count(),
                sprintf('O atalho "%s" precisa resolver para pelo menos uma ferramenta real.', $shortcut['label'] ?? ''),
            );
        }
    }
}
