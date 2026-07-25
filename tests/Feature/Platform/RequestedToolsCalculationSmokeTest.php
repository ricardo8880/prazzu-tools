<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class RequestedToolsCalculationSmokeTest extends TestCase
{
    /** @param array<string, string|int> $payload */
    #[DataProvider('calculationCases')]
    public function test_each_new_public_tool_executes_its_essential_flow(
        string $routeName,
        array $payload,
        string $expectedResult,
    ): void {
        $this->post(route($routeName), $payload)
            ->assertOk()
            ->assertSee('<!doctype html>', false)
            ->assertSee($expectedResult);
    }

    /** @return array<string, array{string, array<string, string|int>, string}> */
    public static function calculationCases(): array
    {
        return [
            'admission' => [
                'tools.simulador-admissao.calculate',
                [
                    'salary' => '3.000,00',
                    'benefits' => '500,00',
                    'monthly_burden' => '40',
                    'exam' => '200,00',
                    'recruitment' => '300,00',
                    'equipment' => '1.000,00',
                    'training' => '400,00',
                ],
                'Custo da admissão',
            ],
            'break even' => [
                'tools.ponto-de-equilibrio.calculate',
                ['fixed_costs' => '10.000,00', 'sale_price' => '100,00', 'variable_cost' => '60,00'],
                'Ponto de equilíbrio',
            ],
            'cash flow' => [
                'tools.fluxo-de-caixa.calculate',
                [
                    'opening_balance' => '1.000,00',
                    'sales_receipts' => '10.000,00',
                    'other_inflows' => '0,00',
                    'operating_payments' => '5.000,00',
                    'tax_payments' => '1.000,00',
                    'investments' => '0,00',
                    'financing_payments' => '0,00',
                    'other_outflows' => '0,00',
                ],
                'Previsão de caixa',
            ],
            'employer inss' => [
                'tools.inss-patronal.calculate',
                ['payroll' => '100.000,00', 'regime' => 'general', 'adjusted_rat' => '1', 'third_parties' => '5.8'],
                'Contribuições patronais',
            ],
            'employment models' => [
                'tools.comparador-clt-pj-autonomo.calculate',
                [
                    'clt_gross' => '5.000,00',
                    'clt_benefits' => '800,00',
                    'clt_employee_deductions' => '11',
                    'clt_company_burden' => '40',
                    'pj_invoice' => '8.000,00',
                    'pj_taxes' => '10',
                    'pj_expenses' => '500,00',
                    'autonomous_gross' => '8.000,00',
                    'autonomous_deductions' => '20',
                    'autonomous_company_burden' => '20',
                ],
                'Comparação',
            ],
            'factor r' => [
                'tools.simulador-fator-r.calculate',
                ['payroll_12' => '30.000,00', 'revenue_12' => '100.000,00'],
                'Resultado do Fator R',
            ],
            'income statement' => [
                'tools.declaracao-rendimentos.calculate',
                [
                    'name' => 'Ana Lima',
                    'document' => '123.456.789-00',
                    'payer' => 'Empresa Exemplo Ltda.',
                    'year' => 2025,
                    'gross' => '60.000,00',
                    'inss' => '6.000,00',
                    'irrf' => '3.000,00',
                    'other_deductions' => '1.000,00',
                ],
                'Declaração de Rendimentos',
            ],
            'labor charges' => [
                'tools.encargos-trabalhistas.calculate',
                ['salary' => '5.000,00', 'benefits' => '800,00', 'regime' => 'general', 'rat' => '1', 'third_parties' => '5.8'],
                'Custo provisionado',
            ],
            'late das' => [
                'tools.das-em-atraso.calculate',
                ['principal' => '1.000,00', 'due_date' => '2026-01-01', 'payment_date' => '2026-02-01', 'accumulated_selic' => '1.5'],
                'Atualização',
            ],
            'payslip' => [
                'tools.gerador-holerite.calculate',
                [
                    'name' => 'Ana Lima',
                    'document' => '123.456.789-00',
                    'employer' => 'Empresa Exemplo Ltda.',
                    'competence' => '2026-07',
                    'salary' => '5.000,00',
                    'other_earnings' => '500,00',
                    'inss' => '500,00',
                    'irrf' => '200,00',
                    'other_deductions' => '100,00',
                ],
                'Holerite',
            ],
            'salary adjustment' => [
                'tools.reajuste-salarial.calculate',
                ['current_salary' => '5.000,00', 'adjustment_rate' => '5', 'fixed_addition' => '100,00', 'retroactive_months' => 3],
                'Resultado do reajuste',
            ],
            'sales commission' => [
                'tools.comissao-vendedores.calculate',
                ['sales' => '100.000,00', 'rate' => '2', 'goal' => '80.000,00', 'goal_bonus_rate' => '0.5'],
                'Resultado da comissão',
            ],
            'work income statement' => [
                'tools.declaracao-trabalho-renda.calculate',
                [
                    'name' => 'Ana Lima',
                    'document' => '123.456.789-00',
                    'employer' => 'Empresa Exemplo Ltda.',
                    'occupation' => 'Analista',
                    'start_date' => '2024-01-10',
                    'monthly_income' => '5.000,00',
                    'city' => 'São Paulo',
                    'issue_date' => '2026-07-25',
                ],
                'Declaração pronta',
            ],
            'working capital' => [
                'tools.capital-de-giro.calculate',
                [
                    'cash' => '20.000,00',
                    'receivables' => '50.000,00',
                    'inventory' => '30.000,00',
                    'other_current_assets' => '5.000,00',
                    'suppliers' => '35.000,00',
                    'other_operating_liabilities' => '10.000,00',
                    'loans' => '15.000,00',
                    'other_current_liabilities' => '5.000,00',
                ],
                'Diagnóstico do capital de giro',
            ],
        ];
    }

    public function test_document_generators_return_validation_errors_instead_of_domain_exceptions(): void
    {
        $this->from(route('tools.declaracao-rendimentos.index'))
            ->post(route('tools.declaracao-rendimentos.calculate'), [
                'name' => 'Ana Lima',
                'document' => '123.456.789-00',
                'payer' => 'Empresa Exemplo Ltda.',
                'year' => 2025,
                'gross' => '1.000,00',
                'inss' => '800,00',
                'irrf' => '500,00',
                'other_deductions' => '0,00',
            ])
            ->assertRedirect(route('tools.declaracao-rendimentos.index'))
            ->assertSessionHasErrors('other_deductions');

        $this->from(route('tools.gerador-holerite.index'))
            ->post(route('tools.gerador-holerite.calculate'), [
                'name' => 'Ana Lima',
                'document' => '123.456.789-00',
                'employer' => 'Empresa Exemplo Ltda.',
                'competence' => '2026-07',
                'salary' => '1.000,00',
                'other_earnings' => '0,00',
                'inss' => '800,00',
                'irrf' => '500,00',
                'other_deductions' => '0,00',
            ])
            ->assertRedirect(route('tools.gerador-holerite.index'))
            ->assertSessionHasErrors('other_deductions');
    }
}
