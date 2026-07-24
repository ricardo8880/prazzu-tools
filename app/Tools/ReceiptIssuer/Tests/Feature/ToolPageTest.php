<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Feature;

use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_tool_page_is_available_with_essential_form(): void
    {
        $this->get(route('tools.emissor-de-recibos.index'))
            ->assertOk()
            ->assertSee('Emissor de Recibos')
            ->assertSee('Dados do recibo')
            ->assertSee('Gerar e revisar recibo');
    }

    public function test_valid_input_generates_complete_receipt_review(): void
    {
        $response = $this->post(route('tools.emissor-de-recibos.issue'), [
            'number' => 'REC-2026-001',
            'payer_name' => 'Empresa Pagadora Ltda.',
            'payer_document_type' => 'cnpj',
            'payer_document' => '04.252.011/0001-10',
            'payee_name' => 'Maria da Silva',
            'payee_document_type' => 'cpf',
            'payee_document' => '529.982.247-25',
            'amount' => '1.250,90',
            'description' => 'Serviços profissionais prestados em julho de 2026',
            'issued_at' => '2026-07-23',
            'city' => 'São Paulo',
        ]);

        $response->assertOk()
            ->assertSee('Revisão do recibo')
            ->assertSee('Empresa Pagadora Ltda.')
            ->assertSee('R$ 1.250,90')
            ->assertSee('mil duzentos e cinquenta reais e noventa centavos');
    }

    public function test_valid_input_opens_dedicated_pdf_export(): void
    {
        $this->post(route('tools.emissor-de-recibos.export.pdf'), [
            'number' => 'REC-2026-003',
            'payer_name' => 'Empresa Pagadora Ltda.',
            'payer_document_type' => 'cnpj',
            'payer_document' => '04.252.011/0001-10',
            'payee_name' => 'Maria da Silva',
            'payee_document_type' => 'cpf',
            'payee_document' => '529.982.247-25',
            'amount' => '1.250,90',
            'description' => 'Serviços profissionais prestados em julho de 2026',
            'issued_at' => '2026-07-23',
            'city' => 'São Paulo',
        ])->assertOk()
            ->assertSee('Recibo nº REC-2026-003')
            ->assertSee('Imprimir / Salvar como PDF')
            ->assertSee('Empresa Pagadora Ltda.')
            ->assertSee('mil duzentos e cinquenta reais e noventa centavos')
            ->assertSee('529.982.247-25');
    }

    public function test_pdf_export_cannot_bypass_central_access_gate(): void
    {
        config()->set('features.tools.emissor-de-recibos.enabled', false);

        $this->post(route('tools.emissor-de-recibos.export.pdf'), [
            'number' => 'REC-2026-004',
            'payer_name' => 'João Pagador',
            'payee_name' => 'Maria Recebedora',
            'amount' => '100,00',
            'description' => 'Pagamento de serviço',
            'issued_at' => '2026-07-23',
        ])->assertServiceUnavailable();
    }

    public function test_invalid_document_is_returned_as_validation_error(): void
    {
        $this->from(route('tools.emissor-de-recibos.index'))
            ->post(route('tools.emissor-de-recibos.issue'), [
                'number' => 'REC-001',
                'payer_name' => 'Empresa Pagadora',
                'payer_document_type' => 'cnpj',
                'payer_document' => '11.111.111/1111-11',
                'payee_name' => 'Maria da Silva',
                'amount' => '100,00',
                'description' => 'Prestação de serviços',
                'issued_at' => '2026-07-23',
            ])
            ->assertRedirect(route('tools.emissor-de-recibos.index'))
            ->assertSessionHasErrors(['receipt']);
    }

    public function test_document_fields_are_optional_as_a_pair(): void
    {
        $this->post(route('tools.emissor-de-recibos.issue'), [
            'number' => 'REC-002',
            'payer_name' => 'João Pagador',
            'payee_name' => 'Maria Recebedora',
            'amount' => '100,00',
            'description' => 'Pagamento de serviço',
            'issued_at' => '2026-07-23',
        ])->assertOk()->assertSee('Revisão do recibo');
    }
}
