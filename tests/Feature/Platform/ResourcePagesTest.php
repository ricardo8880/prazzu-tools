<?php

namespace Tests\Feature\Platform;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ResourcePagesTest extends TestCase
{
    public function test_resources_index_has_specific_content_instead_of_generic_placeholder(): void
    {
        $this->get('/recursos')
            ->assertOk()
            ->assertViewIs('pages.resources.index')
            ->assertSee('Menos volume. Mais utilidade.')
            ->assertSee('Guias práticos')
            ->assertSee('Modelos profissionais');
    }

    public function test_guides_page_uses_the_resource_catalog(): void
    {
        $this->get('/recursos/guias')
            ->assertOk()
            ->assertViewIs('pages.resources.listing')
            ->assertSee('Guia profissional para precificação de honorários contábeis')
            ->assertSee('Publicado')
            ->assertSee('Acessar guia');
    }

    public function test_templates_page_uses_the_resource_catalog(): void
    {
        $this->get('/recursos/modelos')
            ->assertOk()
            ->assertViewIs('pages.resources.listing')
            ->assertSee('Modelo de levantamento para precificação de honorários')
            ->assertSee('Planilha e checklist')
            ->assertSee('Publicado')
            ->assertSee('Acessar modelo');
    }


    public function test_published_guide_has_complete_practical_content(): void
    {
        $this->get('/recursos/guias/precificacao-de-honorarios-contabeis')
            ->assertOk()
            ->assertViewIs('pages.resources.guides.accounting-fees-pricing')
            ->assertSee('Como precificar honorários contábeis com método')
            ->assertSee('Método em 7 etapas')
            ->assertSee('Erros que destroem margem e relacionamento')
            ->assertSee('Checklist antes de enviar a proposta')
            ->assertSee('Abrir Calculadora de Honorários');
    }

    public function test_published_model_has_download_and_usage_guidance(): void
    {
        $this->get('/recursos/modelos/levantamento-para-precificacao-de-honorarios')
            ->assertOk()
            ->assertViewIs('pages.resources.models.accounting-fees-survey')
            ->assertSee('Levantamento para precificação de honorários contábeis')
            ->assertSee('Baixar planilha')
            ->assertSee('não calcula o preço')
            ->assertSee('Abrir calculadora');

        $this->assertFileExists(public_path('downloads/resources/modelo-levantamento-honorarios-contabeis.xlsx'));
    }

    public function test_every_resource_has_the_minimum_editorial_contract(): void
    {
        foreach (config('resources.items', []) as $item) {
            $this->assertContains($item['type'], array_keys(config('resources.sections', [])));
            $this->assertNotEmpty($item['slug']);
            $this->assertNotEmpty($item['title']);
            $this->assertNotEmpty($item['summary']);
            $this->assertNotEmpty($item['category']);
            $this->assertNotEmpty($item['status']);
            $this->assertNotEmpty($item['tool']['name']);
            $this->assertTrue(Route::has($item['tool']['route']));

            if ($item['status'] === 'published') {
                $this->assertNotEmpty($item['reviewed_at']);
                $this->assertNotEmpty($item['view']);
                $this->assertTrue(Route::has($item['route']));
            }
        }
    }

    public function test_published_resources_expose_consistent_seo_and_structured_data(): void
    {
        $this->get('/recursos/guias/precificacao-de-honorarios-contabeis')
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('https://schema.org', false)
            ->assertSee('Como precificar honorários contábeis com método');

        $this->get('/recursos/modelos/levantamento-para-precificacao-de-honorarios')
            ->assertOk()
            ->assertSee('Modelo para levantamento de honorários contábeis')
            ->assertSee('application/ld+json', false);
    }

    public function test_guide_model_and_tool_are_cross_linked(): void
    {
        $this->get('/recursos/guias/precificacao-de-honorarios-contabeis')
            ->assertOk()
            ->assertSee('Aprenda, organize e calcule')
            ->assertSee('Modelo de levantamento para precificação de honorários')
            ->assertSee('Calculadora de Honorários Contábeis');

        $this->get('/recursos/modelos/levantamento-para-precificacao-de-honorarios')
            ->assertOk()
            ->assertSee('Guia profissional para precificação de honorários contábeis')
            ->assertSee('Calculadora de Honorários Contábeis');
    }
}
