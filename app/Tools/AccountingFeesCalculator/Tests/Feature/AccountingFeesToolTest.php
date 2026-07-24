<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->assertOk()
            ->assertSee('Calculadora de Honorários Contábeis')
            ->assertSee('Dados para precificação');
    }

    public function test_calculation_returns_pricing_result(): void
    {
        $this->from(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->post(route('tools.calculadora-de-honorarios-contabeis.calculate'), [
                'monthly_revenue' => '100.000,00',
                'employees' => 5,
                'partners' => 2,
                'monthly_invoices' => 120,
                'monthly_bank_transactions' => 250,
                'tax_regime' => 'simples_nacional',
                'business_segment' => 'commerce',
                'complexity' => 'medium',
            ])
            ->assertOk()
            ->assertSee('R$ 1.678,43')
            ->assertSee('R$ 1.930,19')
            ->assertSee('1.0.0');
    }

    public function test_generates_a_commercial_proposal(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.proposal'), [
            'client_company' => 'Empresa Exemplo Ltda.',
            'client_document' => '12.345.678/0001-90',
            'contact_name' => 'Maria Silva',
            'accounting_firm' => 'Contabilidade Modelo',
            'monthly_fee' => '1.930,19',
            'setup_fee' => '500,00',
            'due_day' => 10,
            'validity_days' => 15,
            'services' => ['accounting', 'tax', 'payroll'],
            'notes' => 'Reunião mensal incluída.',
        ])
            ->assertOk()
            ->assertSee('Proposta comercial')
            ->assertSee('Empresa Exemplo Ltda.')
            ->assertSee('R$ 1.930,19')
            ->assertSee('Escrituração contábil e demonstrações');
    }

    public function test_proposal_requires_at_least_one_service(): void
    {
        $this->from(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->post(route('tools.calculadora-de-honorarios-contabeis.proposal'), [
                'client_company' => 'Empresa Exemplo Ltda.',
                'contact_name' => 'Maria Silva',
                'accounting_firm' => 'Contabilidade Modelo',
                'monthly_fee' => '1.930,19',
                'due_day' => 10,
                'validity_days' => 15,
                'services' => [],
            ])
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->assertSessionHasErrors(['services']);
    }

    public function test_generates_a_service_contract(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.contract'), [
            'client_company' => 'Empresa Exemplo Ltda.',
            'client_document' => '12.345.678/0001-90',
            'client_representative' => 'Maria Silva',
            'accounting_firm' => 'Contabilidade Modelo',
            'accounting_firm_document' => '98.765.432/0001-10',
            'accounting_representative' => 'João Contador',
            'monthly_fee' => '1.930,19',
            'due_day' => 10,
            'start_date' => '2026-08-01',
            'duration_months' => 12,
            'adjustment_index' => 'IPCA',
            'late_fee_percent' => 2,
            'termination_notice_days' => 30,
            'services' => ['accounting', 'tax', 'payroll'],
            'includes_lgpd' => 1,
            'includes_confidentiality' => 1,
        ])
            ->assertOk()
            ->assertSee('Contrato de Prestação de Serviços Contábeis')
            ->assertSee('Empresa Exemplo Ltda.')
            ->assertSee('R$ 1.930,19')
            ->assertSee('Proteção de dados');
    }

    public function test_contract_requires_at_least_one_service(): void
    {
        $this->from(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->post(route('tools.calculadora-de-honorarios-contabeis.contract'), [
                'client_company' => 'Empresa Exemplo Ltda.',
                'client_representative' => 'Maria Silva',
                'accounting_firm' => 'Contabilidade Modelo',
                'accounting_representative' => 'João Contador',
                'monthly_fee' => '1.930,19',
                'due_day' => 10,
                'start_date' => '2026-08-01',
                'duration_months' => 12,
                'adjustment_index' => 'IPCA',
                'late_fee_percent' => 2,
                'termination_notice_days' => 30,
                'services' => [],
            ])
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->assertSessionHasErrors(['services']);
    }

    public function test_invalid_input_returns_validation_errors(): void
    {
        $this->from(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->post(route('tools.calculadora-de-honorarios-contabeis.calculate'), [
                'monthly_revenue' => 'valor inválido',
                'employees' => -1,
                'partners' => 0,
            ])
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'))
            ->assertSessionHasErrors(['monthly_revenue', 'employees', 'partners']);
    }
}
