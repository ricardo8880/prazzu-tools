<?php

namespace Tests\Feature\Platform;

use Tests\TestCase;

final class PlansPageTest extends TestCase
{
    public function test_plans_page_displays_free_and_plus_experiences(): void
    {
        $this->get('/planos')
            ->assertOk()
            ->assertSee('Prazzu Gratuito')
            ->assertSee('Prazzu Plus')
            ->assertSee('R$ 39,90')
            ->assertSee('R$ 109,90')
            ->assertSee('R$ 399,90')
            ->assertSee('Uma única assinatura')
            ->assertSee('uma vez por dia');
    }

    public function test_plans_page_displays_billing_periods_and_savings(): void
    {
        $this->get('/planos')
            ->assertOk()
            ->assertSee('Mensal')
            ->assertSee('Trimestral')
            ->assertSee('Anual')
            ->assertSee('R$ 36,63 por mês')
            ->assertSee('R$ 33,33 por mês')
            ->assertSee('Economia de R$ 9,80 por trimestre')
            ->assertSee('Economia de R$ 78,90 por ano');
    }

    public function test_plans_page_explains_the_platform_model(): void
    {
        $this->get('/planos')
            ->assertOk()
            ->assertSee('A versão gratuita continuará completa?')
            ->assertSee('Preciso assinar cada ferramenta separadamente?')
            ->assertSee('Uma única assinatura do Prazzu Plus libera os recursos Plus de todas as ferramentas');
    }

    public function test_plan_buttons_are_present_but_not_connected_to_checkout(): void
    {
        $this->get('/planos')
            ->assertOk()
            ->assertSee('Assinaturas em breve')
            ->assertSee('Nenhum pagamento será realizado nesta etapa.')
            ->assertDontSee('href="/checkout"', false);
    }
}
