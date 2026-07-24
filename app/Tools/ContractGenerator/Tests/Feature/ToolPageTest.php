<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Feature;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Tools\ToolRegistry;
use App\Tools\ContractGenerator\Tool;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_beta_module_is_registered_and_visible_in_public_catalog(): void
    {
        $registry = $this->app->make(ToolRegistry::class);

        self::assertTrue($registry->has(Tool::SLUG));
        self::assertContains(Tool::SLUG, collect($registry->manifests())->pluck('slug')->all());
    }

    public function test_page_lists_the_initial_contract_types(): void
    {
        $this->get(route('tools.gerador-de-contratos.index'))
            ->assertOk()
            ->assertSee('Gerador de Contratos')
            ->assertSee('Beta')
            ->assertSee('Prestação de serviços')
            ->assertSee('Compra e venda de bem móvel')
            ->assertSee('Escolha uma modalidade acima para iniciar o questionário.');
    }

    public function test_service_questionnaire_is_available(): void
    {
        $this->get(route('tools.gerador-de-contratos.index', ['tipo' => 'prestacao-servicos']))
            ->assertOk()
            ->assertSee('Contratante')
            ->assertSee('Contratado')
            ->assertSee('Quais serviços serão prestados?')
            ->assertSee('Gerar contrato completo');
    }


    public function test_generation_analytics_does_not_receive_contract_personal_data(): void
    {
        $analytics = $this->mock(PlatformAnalytics::class);
        $analytics->shouldReceive('track')
            ->once()
            ->withArgs(function (AnalyticsEvent $event): bool {
                self::assertSame('tool.calculation.completed', $event->name);
                self::assertSame('gerador-de-contratos', $event->properties['subject_slug'] ?? null);
                self::assertSame('prestacao-servicos', $event->properties['contract_type'] ?? null);
                self::assertArrayNotHasKey('first_party_name', $event->properties);
                self::assertArrayNotHasKey('first_party_document', $event->properties);
                self::assertArrayNotHasKey('contract_text', $event->properties);

                return true;
            });

        $this->post(route('tools.gerador-de-contratos.build'), $this->servicePayload())
            ->assertOk();
    }

    public function test_valid_service_answers_generate_editable_contract(): void
    {
        $this->post(route('tools.gerador-de-contratos.build'), $this->servicePayload())
            ->assertOk()
            ->assertSee('Dados conferidos e contrato gerado')
            ->assertSee('Empresa Contratante Ltda.')
            ->assertSee('R$ 2.500,00')
            ->assertSee('CONTRATO PARTICULAR DE PRESTAÇÃO DE SERVIÇOS')
            ->assertSee('dois mil e quinhentos reais')
            ->assertSee('Texto completo do contrato')
            ->assertSee('Atualizar visualização');
    }

    public function test_valid_sale_answers_generate_sale_contract(): void
    {
        $payload = $this->servicePayload();
        $payload['contract_type'] = 'compra-venda-bem-movel';
        unset($payload['service_description'], $payload['start_date'], $payload['end_date'], $payload['termination_notice_days']);
        $payload['asset_description'] = 'Notebook empresarial, número de série ABC123.';
        $payload['delivery_date'] = '2026-08-10';
        $payload['delivery_location'] = 'São Paulo/SP';

        $this->post(route('tools.gerador-de-contratos.build'), $payload)
            ->assertOk()
            ->assertSee('Vendedor')
            ->assertSee('Comprador')
            ->assertSee('CONTRATO PARTICULAR DE COMPRA E VENDA DE BEM MÓVEL')
            ->assertSee('Notebook empresarial, número de série ABC123.')
            ->assertSee('10 de agosto de 2026');
    }

    public function test_contract_can_be_exported_as_printable_pdf_view(): void
    {
        $text = "CONTRATO PARA PDF\n\nCláusula preparada para impressão.";

        $this->post(route('tools.gerador-de-contratos.export.pdf'), [
            'contract_type' => 'prestacao-servicos',
            'contract_text' => $text,
        ])
            ->assertOk()
            ->assertSee('Imprimir / Salvar como PDF')
            ->assertSee('CONTRATO PARA PDF')
            ->assertSee('Cláusula preparada para impressão.');
    }

    public function test_contract_can_be_downloaded_as_docx(): void
    {
        $text = "CONTRATO WORD\n\nCláusula com acentuação & símbolos <seguros>.";

        $response = $this->post(route('tools.gerador-de-contratos.export.docx'), [
            'contract_type' => 'prestacao-servicos',
            'contract_text' => $text,
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        self::assertStringContainsString('.docx', (string) $response->headers->get('content-disposition'));
        self::assertStringStartsWith('PK', (string) $response->getContent());
    }

    public function test_edited_contract_can_be_previewed_without_persistence(): void
    {
        $text = "CONTRATO EDITADO\n\nCláusula personalizada pelo usuário.";

        $this->post(route('tools.gerador-de-contratos.preview'), [
            'contract_type' => 'prestacao-servicos',
            'contract_text' => $text,
        ])
            ->assertOk()
            ->assertSee('Visualização atualizada com o texto editado')
            ->assertSee('CONTRATO EDITADO')
            ->assertSee('Cláusula personalizada pelo usuário.');
    }

    public function test_service_term_longer_than_four_years_returns_validation_error(): void
    {
        $payload = $this->servicePayload();
        $payload['end_date'] = '2030-08-02';

        $this->from(route('tools.gerador-de-contratos.index', ['tipo' => 'prestacao-servicos']))
            ->post(route('tools.gerador-de-contratos.build'), $payload)
            ->assertRedirect(route('tools.gerador-de-contratos.index', ['tipo' => 'prestacao-servicos']))
            ->assertSessionHasErrors(['end_date']);
    }

    public function test_invalid_party_document_returns_validation_error(): void
    {
        $payload = $this->servicePayload();
        $payload['first_party_document'] = '11.111.111/1111-11';

        $this->from(route('tools.gerador-de-contratos.index', ['tipo' => 'prestacao-servicos']))
            ->post(route('tools.gerador-de-contratos.build'), $payload)
            ->assertRedirect(route('tools.gerador-de-contratos.index', ['tipo' => 'prestacao-servicos']))
            ->assertSessionHasErrors(['first_party_document']);
    }

    /** @return array<string, mixed> */
    private function servicePayload(): array
    {
        return [
            'contract_type' => 'prestacao-servicos',
            'first_party_name' => 'Empresa Contratante Ltda.',
            'first_party_document_type' => 'cnpj',
            'first_party_document' => '04.252.011/0001-10',
            'first_party_address' => 'Avenida Paulista, 1000',
            'first_party_city' => 'São Paulo',
            'first_party_state' => 'SP',
            'second_party_name' => 'Maria da Silva',
            'second_party_document_type' => 'cpf',
            'second_party_document' => '529.982.247-25',
            'second_party_address' => 'Rua das Flores, 100',
            'second_party_city' => 'Campinas',
            'second_party_state' => 'SP',
            'amount' => '2.500,00',
            'payment_terms' => 'Pagamento mensal até o quinto dia útil.',
            'service_description' => 'Consultoria contábil mensal.',
            'start_date' => '2026-08-01',
            'end_date' => '2027-07-31',
            'termination_notice_days' => 30,
            'jurisdiction_city' => 'São Paulo',
            'jurisdiction_state' => 'SP',
            'signing_city' => 'São Paulo',
            'signing_date' => '2026-07-24',
            'additional_terms' => 'Reuniões poderão ocorrer por videoconferência.',
        ];
    }
}
