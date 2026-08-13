<?php

declare(strict_types=1);

namespace App\Tools\IssCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_loads(): void
    {
        $this->get('/tools/contabil/ferramentas/calculadora-iss')
            ->assertOk()
            ->assertSee('Calculadora de ISS');
    }

    public function test_calculation_returns_result(): void
    {
        $this->post('/tools/contabil/ferramentas/calculadora-iss', $this->payload())
            ->assertOk()
            ->assertSee('ISS estimado');
    }

    #[CoversPlusFeature('calculadora-iss', 'retention')]
    #[CoversPlusFeature('calculadora-iss', 'multiple_services')]
    #[CoversPlusFeature('calculadora-iss', 'municipality_scenarios')]
    #[CoversPlusFeature('calculadora-iss', 'export')]
    public function test_plus_retention_services_scenarios_and_exports_are_rendered(): void
    {
        $payload = $this->payload();
        $payload['retained'] = '1';
        $payload['services'] = [[
            'competence' => '2026-08',
            'municipality' => 'Campinas/SP',
            'service' => 'Serviço adicional',
            'taker' => 'Cliente adicional',
            'value' => '2.000,00',
            'rate' => '3',
            'retained' => '0',
        ]];
        $payload['municipality_scenarios'] = [[
            'municipality' => 'Rio de Janeiro/RJ',
            'rate' => '4',
        ]];

        $this->post('/tools/contabil/ferramentas/calculadora-iss', $payload)
            ->assertOk()
            ->assertSee('Cenários por município')
            ->assertSee('Rio de Janeiro/RJ')
            ->assertSee('Exportar PDF')
            ->assertSee('Baixar XLSX');
    }

    private function payload(): array
    {
        return [
            'competence' => '2026-08',
            'municipality' => 'São Paulo/SP',
            'service' => 'Consultoria',
            'taker' => 'Cliente principal',
            'value' => '5.000,00',
            'rate' => '5',
            'retained' => '0',
        ];
    }
}
