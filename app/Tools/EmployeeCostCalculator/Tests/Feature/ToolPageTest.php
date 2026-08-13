<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.custo-funcionario-clt.index'))
            ->assertOk()
            ->assertSee('<!doctype html>', false)
            ->assertSee('<link rel="canonical" href="'.route('tools.custo-funcionario-clt.index').'">', false)
            ->assertSee('Calculadora de Custo de Funcionário CLT');
    }

    public function test_individual_calculation_and_print_report_are_available_for_visitors(): void
    {
        $payload = $this->employeePayload();

        $this->post(route('tools.custo-funcionario-clt.calculate'), $payload)
            ->assertOk()
            ->assertSee('Custo mensal provisionado')
            ->assertSee('Custo por hora')
            ->assertSee('Memória de cálculo');

        $this->post(route('tools.custo-funcionario-clt.print'), $payload)
            ->assertOk()
            ->assertSee('<!doctype html>', false)
            ->assertSee('Relatório de Custo de Funcionário CLT')
            ->assertSee('Premissas consideradas')
            ->assertSee('Custo anual projetado');
    }

    #[CoversPlusFeature('custo-funcionario-clt', 'batch_processing')]
    #[CoversPlusFeature('custo-funcionario-clt', 'professional_report')]
    public function test_batch_print_and_scenario_comparison_have_visible_results(): void
    {
        $employee = [
            ...$this->employeePayload(),
            'role' => 'Analista',
        ];

        $this->post(route('tools.custo-funcionario-clt.batch.print'), [
            'scenario_name' => 'Equipe atual',
            'employees' => [$employee],
        ])
            ->assertOk()
            ->assertSee('Relatório Consolidado de Custos CLT')
            ->assertSee('Consolidado por departamento')
            ->assertSee('Projeção-base de 12 meses');

        $alternative = $employee;
        $alternative['salary'] = '5.500,00';

        $this->post(route('tools.custo-funcionario-clt.scenarios.compare'), [
            'scenarios' => [
                ['scenario_name' => 'Atual', 'employees' => [$employee]],
                ['scenario_name' => 'Alternativo', 'employees' => [$alternative]],
            ],
        ])
            ->assertOk()
            ->assertSee('Comparação de cenários')
            ->assertSee('Menor custo anual')
            ->assertSee('Alternativo');
    }

    #[CoversPlusFeature('custo-funcionario-clt', 'scenarios')]
    public function test_two_cost_scenarios_are_compared(): void
    {
        $employee = [...$this->employeePayload(), 'role' => 'Analista'];
        $alternative = [...$employee, 'salary' => '5.500,00'];
        $this->post(route('tools.custo-funcionario-clt.scenarios.compare'), [
            'scenarios' => [
                ['scenario_name' => 'Atual', 'employees' => [$employee]],
                ['scenario_name' => 'Alternativo', 'employees' => [$alternative]],
            ],
        ])->assertOk()->assertSee('Comparação de cenários')->assertSee('Atual')->assertSee('Alternativo');
    }

    #[CoversPlusFeature('custo-funcionario-clt', 'csv_export')]
    #[CoversPlusFeature('custo-funcionario-clt', 'xlsx_export')]
    public function test_csv_and_xlsx_exports_use_the_shared_download_formats(): void
    {
        $payload = $this->employeePayload();

        $this->post(route('tools.custo-funcionario-clt.export.csv'), $payload)
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $xlsx = $this->post(route('tools.custo-funcionario-clt.export.xlsx'), $payload)
            ->assertOk()
            ->assertHeader(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );

        self::assertStringStartsWith('PK', (string) $xlsx->getContent());

        $this->get(route('tools.custo-funcionario-clt.import.template.xlsx'))
            ->assertOk()
            ->assertHeader(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
    }

    /** @return array<string, string> */
    private function employeePayload(): array
    {
        return [
            'employee_name' => 'Ana Lima',
            'department' => 'Contábil',
            'salary' => '5.000,00',
            'variable_pay' => '0,00',
            'benefits' => '800,00',
            'regime' => 'general',
            'rat' => '1',
            'third_parties' => '5.8',
            'monthly_hours' => '220',
        ];
    }
}
