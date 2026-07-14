<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Tools\BusinessDocumentValidator\Infrastructure\Providers\BrasilApiCompanyRegistryProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class BrasilApiCompanyRegistryProviderTest extends TestCase
{
    public function test_it_maps_a_successful_registry_response(): void
    {
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/04252011000110' => Http::response([
                'razao_social' => 'EMPRESA EXEMPLO LTDA',
                'nome_fantasia' => 'EXEMPLO',
                'descricao_situacao_cadastral' => 'ATIVA',
                'data_situacao_cadastral' => '2005-11-03',
                'data_inicio_atividade' => '2005-11-03',
                'natureza_juridica' => 'Sociedade Empresária Limitada',
                'descricao_identificador_matriz_filial' => 'MATRIZ',
                'cnae_fiscal' => 6201501,
                'cnae_fiscal_descricao' => 'Desenvolvimento de programas de computador sob encomenda',
                'cnaes_secundarios' => [
                    ['codigo' => 6202300, 'descricao' => 'Desenvolvimento e licenciamento de programas customizáveis'],
                ],
                'logradouro' => 'RUA EXEMPLO',
                'numero' => '100',
                'complemento' => 'SALA 1',
                'bairro' => 'CENTRO',
                'municipio' => 'SAO PAULO',
                'uf' => 'SP',
                'cep' => '01001000',
            ], 200),
        ]);

        $result = (new BrasilApiCompanyRegistryProvider())->lookup('04252011000110');

        self::assertSame('found', $result->status->value);
        self::assertSame('EMPRESA EXEMPLO LTDA', $result->company?->legalName);
        self::assertSame('ATIVA', $result->company?->registrationStatus);
        self::assertSame('SP', $result->company?->state);
        self::assertCount(1, $result->company?->secondaryActivities ?? []);
    }

    public function test_it_reports_not_found(): void
    {
        Http::fake(['*' => Http::response(['message' => 'not found'], 404)]);

        $result = (new BrasilApiCompanyRegistryProvider())->lookup('04252011000110');

        self::assertSame('not_found', $result->status->value);
    }

    public function test_it_reports_provider_unavailability(): void
    {
        Http::fake(['*' => Http::response([], 503)]);

        $result = (new BrasilApiCompanyRegistryProvider())->lookup('04252011000110');

        self::assertSame('unavailable', $result->status->value);
    }
}
