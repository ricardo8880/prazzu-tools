<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use Tests\TestCase;

final class ToolAcquisitionSeoTest extends TestCase
{
    public function test_standard_tool_page_exposes_canonical_structured_data_and_trust_content(): void
    {
        $canonical = route('tools.calculadora-salario-liquido.index');

        $this->get($canonical)
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$canonical.'">', false)
            ->assertSee('"@type":"WebApplication"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('O que conferir antes de usar o resultado')
            ->assertSee('Conta é opcional')
            ->assertSee('Versão da ferramenta: 1.0.0')
            ->assertSee('INSS 2026', false);
    }

    public function test_legacy_tool_page_keeps_its_specific_guidance_and_receives_structured_data(): void
    {
        $this->get(route('tools.calculadora-ferias.index'))
            ->assertOk()
            ->assertSee('"@type":"WebApplication"', false)
            ->assertSee('Como interpretar')
            ->assertDontSee('O que conferir antes de usar o resultado');
    }

    public function test_footer_uses_verifiable_product_principles_instead_of_future_metrics(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Uso imediato')
            ->assertSee('Sem cadastro obrigatório')
            ->assertSee('Essencial completo')
            ->assertSee('Catálogo em evolução')
            ->assertDontSee('<strong>+120</strong>', false)
            ->assertDontSee('<strong>+50k</strong>', false)
            ->assertDontSee('<strong>100%</strong>', false)
            ->assertDontSee('<strong>Sempre</strong><small>Atualizado</small>', false);
    }
}
