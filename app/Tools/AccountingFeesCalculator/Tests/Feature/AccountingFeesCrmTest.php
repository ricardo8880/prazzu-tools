<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_page_is_available(): void
    {
        $this->get(route('tools.calculadora-de-honorarios-contabeis.crm.index'))
            ->assertOk()
            ->assertSee('CRM de honorários');
    }

    public function test_it_stores_a_client_in_the_crm(): void
    {
        $response = $this->post(route('tools.calculadora-de-honorarios-contabeis.crm.store'), [
            'company_name' => 'Empresa Exemplo Ltda.',
            'document' => '12.345.678/0001-90',
            'contact_name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone' => '(11) 99999-9999',
            'monthly_fee' => '1.500,00',
            'proposal_status' => 'sent',
            'contract_status' => 'draft',
            'pipeline_status' => 'negotiation',
            'notes' => 'Retornar na próxima semana.',
        ]);

        $response->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.crm.index'));

        $client = AccountingClient::query()->firstOrFail();
        self::assertSame('Empresa Exemplo Ltda.', $client->company_name);
        self::assertSame(150000, $client->monthly_fee_cents);
        self::assertSame('negotiation', $client->pipeline_status);
    }

    public function test_it_filters_clients_by_pipeline_status(): void
    {
        $session = ['accounting_fees_crm_key' => '7a5c09c2-742e-4d03-a82f-19d0b31a9d40'];

        AccountingClient::query()->create([
            'session_key' => $session['accounting_fees_crm_key'],
            'company_name' => 'Cliente Ativo',
            'contact_name' => 'João',
            'monthly_fee_cents' => 200000,
            'proposal_status' => 'accepted',
            'contract_status' => 'signed',
            'pipeline_status' => 'client',
        ]);

        AccountingClient::query()->create([
            'session_key' => $session['accounting_fees_crm_key'],
            'company_name' => 'Prospect Novo',
            'contact_name' => 'Ana',
            'monthly_fee_cents' => 90000,
            'proposal_status' => 'not_created',
            'contract_status' => 'not_created',
            'pipeline_status' => 'prospect',
        ]);

        $this->withSession($session)
            ->get(route('tools.calculadora-de-honorarios-contabeis.crm.index', ['status' => 'client']))
            ->assertOk()
            ->assertSee('Cliente Ativo')
            ->assertDontSee('Prospect Novo');
    }
}
