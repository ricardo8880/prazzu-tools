<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Infrastructure\Providers;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyActivity;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryData;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Throwable;

final class BrasilApiCompanyRegistryProvider implements CompanyRegistryProvider
{
    public function lookup(string $cnpj): CompanyRegistryLookupResult
    {
        try {
            $response = Http::baseUrl((string) config('services.brasil_api.base_url', 'https://brasilapi.com.br/api'))
                ->acceptJson()
                ->connectTimeout((int) config('services.brasil_api.connect_timeout', 3))
                ->timeout((int) config('services.brasil_api.timeout', 8))
                ->retry(2, 250, throw: false)
                ->get('/cnpj/v1/'.$cnpj);
        } catch (ConnectionException) {
            return CompanyRegistryLookupResult::unavailable();
        } catch (Throwable) {
            return CompanyRegistryLookupResult::unavailable('Não foi possível concluir a consulta cadastral neste momento.');
        }

        if ($response->status() === 404) {
            return CompanyRegistryLookupResult::notFound();
        }

        if (! $response->successful()) {
            return CompanyRegistryLookupResult::unavailable();
        }

        $payload = $response->json();

        if (! is_array($payload) || ! is_string(Arr::get($payload, 'razao_social'))) {
            return CompanyRegistryLookupResult::unavailable('O provedor retornou dados cadastrais em formato inesperado.');
        }

        return CompanyRegistryLookupResult::found($this->mapCompany($payload, $cnpj));
    }

    /** @param array<string, mixed> $payload */
    private function mapCompany(array $payload, string $cnpj): CompanyRegistryData
    {
        $secondaryActivities = collect(Arr::get($payload, 'cnaes_secundarios', []))
            ->filter(static fn (mixed $activity): bool => is_array($activity))
            ->map(static fn (array $activity): CompanyActivity => new CompanyActivity(
                code: (string) Arr::get($activity, 'codigo', ''),
                description: (string) Arr::get($activity, 'descricao', ''),
            ))
            ->values()
            ->all();

        $primaryCode = $this->nullableString(Arr::get($payload, 'cnae_fiscal'));
        $primaryDescription = $this->nullableString(Arr::get($payload, 'cnae_fiscal_descricao'));

        return new CompanyRegistryData(
            cnpj: $cnpj,
            legalName: (string) Arr::get($payload, 'razao_social'),
            tradeName: $this->nullableString(Arr::get($payload, 'nome_fantasia')),
            registrationStatus: $this->nullableString(Arr::get($payload, 'descricao_situacao_cadastral')),
            registrationStatusDate: $this->nullableString(Arr::get($payload, 'data_situacao_cadastral')),
            openingDate: $this->nullableString(Arr::get($payload, 'data_inicio_atividade')),
            legalNature: $this->nullableString(Arr::get($payload, 'natureza_juridica')),
            branchType: $this->nullableString(Arr::get($payload, 'descricao_identificador_matriz_filial')),
            primaryActivity: $primaryCode !== null || $primaryDescription !== null
                ? new CompanyActivity($primaryCode ?? '', $primaryDescription ?? '')
                : null,
            secondaryActivities: $secondaryActivities,
            street: $this->nullableString(Arr::get($payload, 'logradouro')),
            number: $this->nullableString(Arr::get($payload, 'numero')),
            complement: $this->nullableString(Arr::get($payload, 'complemento')),
            district: $this->nullableString(Arr::get($payload, 'bairro')),
            city: $this->nullableString(Arr::get($payload, 'municipio')),
            state: $this->nullableString(Arr::get($payload, 'uf')),
            postalCode: $this->nullableString(Arr::get($payload, 'cep')),
            source: 'BrasilAPI',
            consultedAt: now()->toIso8601String(),
        );
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
