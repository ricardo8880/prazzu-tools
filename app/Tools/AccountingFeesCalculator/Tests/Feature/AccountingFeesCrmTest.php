<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Models\User;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_page_is_available(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('tools.calculadora-de-honorarios-contabeis.crm.index'))
            ->assertOk()
            ->assertSee('CRM de honorários');
    }

    public function test_it_stores_a_client_in_the_crm(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('tools.calculadora-de-honorarios-contabeis.crm.store'), [
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
        self::assertSame($user->getAuthIdentifier(), $client->user_id);
    }

    public function test_it_filters_clients_by_pipeline_status(): void
    {
        $user = User::factory()->create();

        AccountingClient::query()->create([
            'user_id' => $user->getAuthIdentifier(),
            'company_name' => 'Cliente Ativo',
            'contact_name' => 'João',
            'monthly_fee_cents' => 200000,
            'proposal_status' => 'accepted',
            'contract_status' => 'signed',
            'pipeline_status' => 'client',
        ]);

        AccountingClient::query()->create([
            'user_id' => $user->getAuthIdentifier(),
            'company_name' => 'Prospect Novo',
            'contact_name' => 'Ana',
            'monthly_fee_cents' => 90000,
            'proposal_status' => 'not_created',
            'contract_status' => 'not_created',
            'pipeline_status' => 'prospect',
        ]);

        $this->actingAs($user)
            ->get(route('tools.calculadora-de-honorarios-contabeis.crm.index', ['status' => 'client']))
            ->assertOk()
            ->assertSee('Cliente Ativo')
            ->assertDontSee('Prospect Novo');
    }

    public function test_it_updates_and_deletes_only_an_owned_client_with_exact_money(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $client = AccountingClient::query()->create([
            'user_id' => $owner->getAuthIdentifier(),
            'company_name' => 'Cliente Antigo',
            'contact_name' => 'Maria',
            'monthly_fee_cents' => 100000,
            'proposal_status' => 'draft',
            'contract_status' => 'draft',
            'pipeline_status' => 'prospect',
        ]);

        $this->actingAs($owner)
            ->put(route('tools.calculadora-de-honorarios-contabeis.crm.update', $client), [
                'company_name' => 'Cliente Atualizado',
                'contact_name' => 'Maria',
                'email' => 'MARIA@EXAMPLE.COM',
                'monthly_fee' => '9.007.199.254.740,99',
                'proposal_status' => 'accepted',
                'contract_status' => 'signed',
                'pipeline_status' => 'client',
            ])
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.crm.index'));

        $client->refresh();
        self::assertSame(900_719_925_474_099, $client->monthly_fee_cents);
        self::assertSame('maria@example.com', $client->email);

        $this->actingAs($otherUser)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.crm.delete', $client))
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.crm.delete', $client))
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.crm.index'));

        $this->assertDatabaseMissing('accounting_fee_clients', ['id' => $client->getKey()]);
    }
}
