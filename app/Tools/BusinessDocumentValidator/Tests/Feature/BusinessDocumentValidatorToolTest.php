<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Feature;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyActivity;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryData;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class BusinessDocumentValidatorToolTest extends TestCase
{
    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.validador-de-cnpj.index'))
            ->assertOk()
            ->assertSee('Validador Inteligente de CNPJ, CPF e IE')
            ->assertSee('Validação individual')
            ->assertSee('Detecção automática');
    }

    public function test_it_validates_a_cpf(): void
    {
        $this->post(route('tools.validador-de-cnpj.validate'), [
            'document_type' => 'automatic',
            'document_number' => '529.982.247-25',
        ])->assertOk()->assertSee('529.982.247-25')->assertSee('CPF');
    }

    public function test_it_validates_a_cnpj(): void
    {
        $this->post(route('tools.validador-de-cnpj.validate'), [
            'document_type' => 'cnpj',
            'document_number' => '04.252.011/0001-10',
        ])->assertOk()->assertSee('04.252.011/0001-10')->assertSee('CNPJ');
    }

    public function test_it_reports_an_invalid_document(): void
    {
        $this->post(route('tools.validador-de-cnpj.validate'), [
            'document_type' => 'automatic',
            'document_number' => '111.111.111-11',
        ])->assertOk()->assertSee('Inválido');
    }

    public function test_it_rejects_unsupported_characters(): void
    {
        $this->post(route('tools.validador-de-cnpj.validate'), [
            'document_type' => 'automatic',
            'document_number' => 'abc52998224725',
        ])->assertSessionHasErrors('document_number');
    }

    public function test_it_consults_company_registry_data(): void
    {
        $this->app->instance(CompanyRegistryProvider::class, new class implements CompanyRegistryProvider
        {
            public function lookup(string $cnpj): CompanyRegistryLookupResult
            {
                return CompanyRegistryLookupResult::found(new CompanyRegistryData(
                    cnpj: $cnpj,
                    legalName: 'EMPRESA EXEMPLO LTDA',
                    tradeName: 'EXEMPLO',
                    registrationStatus: 'ATIVA',
                    registrationStatusDate: '2020-01-01',
                    openingDate: '2010-01-01',
                    legalNature: 'Sociedade Empresária Limitada',
                    branchType: 'MATRIZ',
                    primaryActivity: new CompanyActivity('6201501', 'Desenvolvimento de software'),
                    secondaryActivities: [],
                    street: 'RUA EXEMPLO',
                    number: '100',
                    complement: null,
                    district: 'CENTRO',
                    city: 'SAO PAULO',
                    state: 'SP',
                    postalCode: '01001000',
                    source: 'Teste',
                    consultedAt: '2026-07-14T12:00:00-03:00',
                ));
            }
        });

        $this->post(route('tools.validador-de-cnpj.lookup-company'), [
            'cnpj' => '04.252.011/0001-10',
        ])->assertOk()->assertSee('EMPRESA EXEMPLO LTDA');
    }

    public function test_it_does_not_consult_invalid_cnpj(): void
    {
        $this->post(route('tools.validador-de-cnpj.lookup-company'), [
            'cnpj' => '11.111.111/1111-11',
        ])->assertOk()->assertSee('matematicamente inválido');
    }

    public function test_state_registration_validation_route_is_available(): void
    {
        $response = $this->post(route('tools.validador-de-cnpj.validate-state-registration'), [
            'state' => 'SP',
            'state_registration' => '110042490114',
        ]);

        $response->assertOk()->assertSee('Válida');
    }

    public function test_it_analyzes_company_inconsistencies(): void
    {
        $this->app->instance(CompanyRegistryProvider::class, new class implements CompanyRegistryProvider
        {
            public function lookup(string $cnpj): CompanyRegistryLookupResult
            {
                return CompanyRegistryLookupResult::found(new CompanyRegistryData(
                    cnpj: $cnpj,
                    legalName: 'EMPRESA EXEMPLO LTDA',
                    tradeName: 'EXEMPLO',
                    registrationStatus: 'ATIVA',
                    registrationStatusDate: '2020-01-01',
                    openingDate: '2010-01-01',
                    legalNature: 'Sociedade Empresária Limitada',
                    branchType: 'MATRIZ',
                    primaryActivity: null,
                    secondaryActivities: [],
                    street: 'RUA EXEMPLO',
                    number: '100',
                    complement: null,
                    district: 'CENTRO',
                    city: 'SAO PAULO',
                    state: 'SP',
                    postalCode: '01001000',
                    source: 'Teste',
                    consultedAt: '2026-07-14T12:00:00-03:00',
                ));
            }
        });

        $this->post(route('tools.validador-de-cnpj.analyze-consistency'), [
            'analysis_cnpj' => '04.252.011/0001-10',
            'legal_name' => 'EMPRESA DIFERENTE LTDA',
            'analysis_state' => 'RJ',
            'city' => 'RIO DE JANEIRO',
        ])->assertOk()->assertSee('EMPRESA DIFERENTE LTDA')->assertSee('Inconsistências');
    }

    public function test_it_previews_a_csv_batch_import(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent(
            'empresas.csv',
            "CNPJ;Razão Social;UF\n04.252.011/0001-10;Empresa Exemplo;SP\n",
        );

        $this->post(route('tools.validador-de-cnpj.batch.preview'), [
            'batch_file' => $file,
        ])->assertOk()
            ->assertSee('empresas.csv')
            ->assertSee('Processar 1 registro(s)')
            ->assertSee('name="import_token"', false);
    }

    public function test_consistency_analysis_requires_state_when_ie_is_informed(): void
    {
        $this->post(route('tools.validador-de-cnpj.analyze-consistency'), [
            'analysis_cnpj' => '04.252.011/0001-10',
            'analysis_state_registration' => '110042490114',
        ])->assertSessionHasErrors('analysis_state');
    }
}
