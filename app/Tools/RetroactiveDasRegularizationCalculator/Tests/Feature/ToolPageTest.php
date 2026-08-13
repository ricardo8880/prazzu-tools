<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_loads(): void
    {
        $this->get('/tools/contabil/ferramentas/calculadora-das-retroativo-regularizacao-simples')
            ->assertOk()
            ->assertSee('DAS Retroativo');
    }

    public function test_calculation_returns_result(): void
    {
        $this->post('/tools/contabil/ferramentas/calculadora-das-retroativo-regularizacao-simples', $this->payload())
            ->assertOk()
            ->assertSee('DAS atualizado estimado');
    }

    #[CoversPlusFeature('calculadora-das-retroativo-regularizacao-simples', 'multiple_competencies')]
    #[CoversPlusFeature('calculadora-das-retroativo-regularizacao-simples', 'report')]
    public function test_multiple_competencies_are_consolidated_and_exports_are_rendered(): void
    {
        $payload = $this->payload();
        $payload['competencies'] = [[
            'competence' => '2026-05',
            'revenue' => '10.000,00',
            'effective_rate' => '6',
            'due_date' => '2026-06-20',
            'update_date' => '2026-08-12',
            'accumulated_selic' => '2',
        ]];

        $this->post('/tools/contabil/ferramentas/calculadora-das-retroativo-regularizacao-simples', $payload)
            ->assertOk()
            ->assertSee('R$ 1.800,00')
            ->assertSee('Exportar PDF')
            ->assertSee('Baixar XLSX');
    }

    private function payload(): array
    {
        return [
            'competence' => '2026-06',
            'revenue' => '20.000,00',
            'effective_rate' => '6',
            'due_date' => '2026-07-20',
            'update_date' => '2026-08-12',
            'accumulated_selic' => '1,12',
            'regularization_months' => 1,
        ];
    }
}
