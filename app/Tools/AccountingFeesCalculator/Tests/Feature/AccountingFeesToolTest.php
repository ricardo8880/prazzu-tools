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
        $this->get(route('tools.accounting-fee-calculator.index'))
            ->assertOk()
            ->assertSee('Accounting Fee Calculator')
            ->assertSee('Client profile');
    }

    public function test_calculation_returns_pricing_result(): void
    {
        $this->from(route('tools.accounting-fee-calculator.index'))
            ->post(route('tools.accounting-fee-calculator.calculate'), [
                'monthly_revenue' => '100000.00',
                'employees' => 5,
                'partners' => 2,
                'monthly_invoices' => 120,
                'monthly_bank_transactions' => 250,
                'tax_regime' => 'sole_proprietor',
                'business_segment' => 'commerce',
                'complexity' => 'medium',
            ])
            ->assertOk()
            ->assertSee('$1,678.43')
            ->assertSee('$1,930.19')
            ->assertSee('1.0.0');
    }

    public function test_generates_a_commercial_proposal(): void
    {
        $this->post(route('tools.accounting-fee-calculator.proposal'), [
            'client_company' => 'Example Company LLC',
            'client_document' => '12-3456789',
            'contact_name' => 'Maria Smith',
            'accounting_firm' => 'Example Accounting LLC',
            'monthly_fee' => '1930.19',
            'setup_fee' => '500.00',
            'due_day' => 10,
            'validity_days' => 15,
            'services' => ['accounting', 'tax', 'payroll'],
            'notes' => 'Monthly review meeting included.',
        ])
            ->assertOk()
            ->assertSee('Commercial proposal')
            ->assertSee('Example Company LLC')
            ->assertSee('$1,930.19')
            ->assertSee('Bookkeeping and financial statements');
    }

    public function test_proposal_requires_at_least_one_service(): void
    {
        $this->from(route('tools.accounting-fee-calculator.index'))
            ->post(route('tools.accounting-fee-calculator.proposal'), [
                'client_company' => 'Example Company LLC',
                'contact_name' => 'Maria Smith',
                'accounting_firm' => 'Example Accounting LLC',
                'monthly_fee' => '1930.19',
                'due_day' => 10,
                'validity_days' => 15,
                'services' => [],
            ])
            ->assertRedirect(route('tools.accounting-fee-calculator.index'))
            ->assertSessionHasErrors(['services']);
    }

    public function test_generates_a_service_contract(): void
    {
        $this->post(route('tools.accounting-fee-calculator.contract'), [
            'client_company' => 'Example Company LLC',
            'client_document' => '12-3456789',
            'client_representative' => 'Maria Smith',
            'accounting_firm' => 'Example Accounting LLC',
            'accounting_firm_document' => '98-7654321',
            'accounting_representative' => 'John Accountant',
            'monthly_fee' => '1930.19',
            'due_day' => 10,
            'start_date' => '2026-08-01',
            'duration_months' => 12,
            'adjustment_index' => 'CPI-U',
            'late_fee_percent' => 2,
            'termination_notice_days' => 30,
            'services' => ['accounting', 'tax', 'payroll'],
            'includes_data_privacy' => 1,
            'includes_confidentiality' => 1,
        ])
            ->assertOk()
            ->assertSee('Accounting Services Agreement')
            ->assertSee('Example Company LLC')
            ->assertSee('$1,930.19')
            ->assertSee('Data privacy and security');
    }

    public function test_contract_requires_at_least_one_service(): void
    {
        $this->from(route('tools.accounting-fee-calculator.index'))
            ->post(route('tools.accounting-fee-calculator.contract'), [
                'client_company' => 'Example Company LLC',
                'client_representative' => 'Maria Smith',
                'accounting_firm' => 'Example Accounting LLC',
                'accounting_representative' => 'John Accountant',
                'monthly_fee' => '1930.19',
                'due_day' => 10,
                'start_date' => '2026-08-01',
                'duration_months' => 12,
                'adjustment_index' => 'CPI-U',
                'late_fee_percent' => 2,
                'termination_notice_days' => 30,
                'services' => [],
            ])
            ->assertRedirect(route('tools.accounting-fee-calculator.index'))
            ->assertSessionHasErrors(['services']);
    }

    public function test_invalid_input_returns_validation_errors(): void
    {
        $this->from(route('tools.accounting-fee-calculator.index'))
            ->post(route('tools.accounting-fee-calculator.calculate'), [
                'monthly_revenue' => 'valor inválido',
                'employees' => -1,
                'partners' => 0,
            ])
            ->assertRedirect(route('tools.accounting-fee-calculator.index'))
            ->assertSessionHasErrors(['monthly_revenue', 'employees', 'partners']);
    }
}
