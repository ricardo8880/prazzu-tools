<?php

namespace Tests\Feature\Platform;

use Tests\TestCase;

final class NavigationTest extends TestCase
{
    public function test_public_navigation_pages_are_available(): void
    {
        foreach (['/', '/ferramentas', '/blog', '/planos', '/recursos', '/sobre', '/entrar', '/criar-conta', '/sugerir-ferramenta'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_catalog_can_be_searched_and_filtered(): void
    {
        $this->get('/ferramentas?q=CNPJ')
            ->assertOk()
            ->assertSee('Validador de CNPJ')
            ->assertDontSee('Gerador de Contrato');

        $this->get('/ferramentas/calculadoras')
            ->assertOk()
            ->assertSee('Calculadora de Impostos')
            ->assertDontSee('Validador de CNPJ');
    }

    public function test_tool_placeholder_page_is_available(): void
    {
        $this->get('/ferramentas/validador-de-cnpj')
            ->assertOk()
            ->assertSee('Módulo preparado');
    }

    public function test_newsletter_validates_and_accepts_email(): void
    {
        $this->from('/')
            ->post('/newsletter', ['email' => 'contador@example.com'])
            ->assertRedirect('/')
            ->assertSessionHas('status');
    }
}
