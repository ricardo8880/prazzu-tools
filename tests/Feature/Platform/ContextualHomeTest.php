<?php

namespace Tests\Feature\Platform;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextToolPlacement;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContextualHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_keeps_default_content_without_context(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('home.hero.title_before'))
            ->assertSee(config('home.cta.label'));
    }

    public function test_active_context_replaces_home_content_and_orders_tools(): void
    {
        $tools = app(\App\Core\Tools\ToolCatalog::class)->all(false)->take(3)->values();
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Rescisão Instagram',
            'keyword' => 'rescisao-video-01',
            'status' => 'active',
            'hero_title_before' => 'Calcule agora',
            'hero_title_line' => 'a sua',
            'hero_title_highlight' => 'rescisão',
            'hero_description' => 'Uma jornada preparada para rescisões.',
            'cta_title' => 'Comece pela ferramenta certa',
            'cta_label' => 'Calcular rescisão',
            'primary_tool_slug' => $tools[1]['slug'],
            'cta_tool_slug' => $tools[1]['slug'],
        ]);

        $context->tools()->create([
            'tool_slug' => $tools[2]['slug'],
            'placement' => AcquisitionContextToolPlacement::Featured,
            'position' => 0,
        ]);
        $context->tools()->create([
            'tool_slug' => $tools[0]['slug'],
            'placement' => AcquisitionContextToolPlacement::Featured,
            'position' => 1,
        ]);

        $response = $this->followingRedirects()->get('/?context=rescisao-video-01');

        $response->assertOk()
            ->assertSee('Calcule agora')
            ->assertSee('Uma jornada preparada para rescisões.')
            ->assertSee('Calcular rescisão')
            ->assertSeeInOrder([
                $tools[1]['name'],
                $tools[2]['name'],
                $tools[0]['name'],
            ]);
    }


    public function test_active_context_can_replace_tools_section_title(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Contexto com título de ferramentas',
            'keyword' => 'titulo-ferramentas',
            'status' => 'active',
            'tools_section_title' => 'Ferramentas para sua rescisão',
        ]);

        $this->followingRedirects()->get('/?context=titulo-ferramentas')
            ->assertOk()
            ->assertSee('Ferramentas para sua rescisão')
            ->assertDontSee('Ferramentas mais recentes');
    }

    public function test_empty_tools_section_title_keeps_default_title(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Contexto sem título de ferramentas',
            'keyword' => 'sem-titulo-ferramentas',
            'status' => 'active',
            'tools_section_title' => null,
        ]);

        $this->followingRedirects()->get('/?context=sem-titulo-ferramentas')
            ->assertOk()
            ->assertSee(config('home.tools_section_title'));
    }

    public function test_inactive_or_unknown_context_uses_complete_default_home(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Campanha inativa',
            'keyword' => 'campanha-inativa',
            'status' => 'inactive',
            'hero_title_before' => 'Não deve aparecer',
        ]);

        $this->followingRedirects()->get('/?context=campanha-inativa')
            ->assertOk()
            ->assertSee(config('home.hero.title_before'))
            ->assertDontSee('Não deve aparecer');

        $this->followingRedirects()->get('/?context=nao-existe')
            ->assertOk()
            ->assertSee(config('home.hero.title_before'));
    }

    public function test_malformed_context_query_uses_default_home(): void
    {
        $this->get('/?context[]=valor')
            ->assertOk()
            ->assertSee(config('home.hero.title_before'));
    }

    public function test_empty_context_fields_fall_back_individually_to_default_content(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Contexto parcial',
            'keyword' => 'contexto-parcial',
            'status' => 'active',
            'hero_title_highlight' => 'personalizado',
        ]);

        $this->followingRedirects()->get('/?context=contexto-parcial')
            ->assertOk()
            ->assertSee(config('home.hero.title_before'))
            ->assertSee(config('home.hero.title_line'))
            ->assertSee('personalizado')
            ->assertSee(config('home.cta.label'));
    }

    public function test_cached_context_is_invalidated_after_editing(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Contexto em cache',
            'keyword' => 'contexto-cache',
            'status' => 'active',
            'hero_title_before' => 'Conteúdo antigo',
        ]);

        $this->followingRedirects()->get('/?context=contexto-cache')->assertSee('Conteúdo antigo');

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Core\Access\Enums\AccountRole::Administrator,
        ]);

        $this->actingAs($admin)->put(route('admin.acquisition.contexts.update', $context->getKey()), [
            'name' => 'Contexto em cache',
            'keyword' => 'contexto-cache',
            'status' => 'active',
            'hero_title_before' => 'Conteúdo atualizado',
        ])->assertRedirect();

        $this->followingRedirects()->get('/?context=contexto-cache')
            ->assertSee('Conteúdo atualizado')
            ->assertDontSee('Conteúdo antigo');
    }

    public function test_cached_active_context_is_invalidated_when_disabled(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Contexto ativo',
            'keyword' => 'contexto-toggle-cache',
            'status' => 'active',
            'hero_title_before' => 'Conteúdo contextual',
        ]);

        $this->followingRedirects()->get('/?context=contexto-toggle-cache')->assertSee('Conteúdo contextual');

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Core\Access\Enums\AccountRole::Administrator,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.acquisition.contexts.toggle', $context->getKey()))
            ->assertRedirect();

        $this->followingRedirects()->get('/?context=contexto-toggle-cache')
            ->assertSee(config('home.hero.title_before'))
            ->assertDontSee('Conteúdo contextual');
    }

    public function test_negative_cache_is_invalidated_when_context_is_created(): void
    {
        $this->followingRedirects()->get('/?context=contexto-novo')
            ->assertSee(config('home.hero.title_before'));

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Core\Access\Enums\AccountRole::Administrator,
        ]);

        $this->actingAs($admin)->post(route('admin.acquisition.contexts.store'), [
            'name' => 'Contexto novo',
            'keyword' => 'contexto-novo',
            'status' => 'active',
            'hero_title_before' => 'Contexto recém-criado',
        ])->assertRedirect();

        $this->followingRedirects()->get('/?context=contexto-novo')->assertSee('Contexto recém-criado');
    }

    public function test_invalid_referenced_tools_are_ignored_without_breaking_home(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Referência inválida',
            'keyword' => 'referencia-invalida',
            'status' => 'active',
            'primary_tool_slug' => 'ferramenta-removida',
        ]);

        $context->tools()->create([
            'tool_slug' => 'outra-ferramenta-removida',
            'placement' => AcquisitionContextToolPlacement::Featured,
            'position' => 0,
        ]);

        $this->followingRedirects()->get('/?context=referencia-invalida')
            ->assertOk()
            ->assertDontSee('ferramenta-removida');
    }
}
