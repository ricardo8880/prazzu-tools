<?php

namespace Tests\Feature\Platform;

use Tests\TestCase;

final class AboutPageTest extends TestCase
{
    public function test_about_page_presents_the_platform_vision(): void
    {
        $this->get('/sobre')
            ->assertOk()
            ->assertSee('Ferramentas contábeis que resolvem de verdade')
            ->assertSee('Uma plataforma, não apenas calculadoras')
            ->assertSee('Cada ferramenta evolui sozinha. A plataforma evolui para todas.')
            ->assertSee('Um único plano');
    }

    public function test_about_page_explains_the_free_and_plus_philosophy(): void
    {
        $this->get('/sobre')
            ->assertOk()
            ->assertSee('Boa · Gratuita')
            ->assertSee('Excelente · Prazzu Plus')
            ->assertSee('A versão gratuita resolve o problema')
            ->assertSee('paga para trabalhar melhor');
    }

    public function test_about_page_links_to_tools_and_plans(): void
    {
        $this->get('/sobre')
            ->assertOk()
            ->assertSee(route('tools.index'), false)
            ->assertSee(route('plans'), false);
    }
}
