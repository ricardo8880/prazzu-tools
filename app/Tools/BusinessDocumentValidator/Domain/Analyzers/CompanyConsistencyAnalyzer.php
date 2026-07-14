<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Analyzers;

use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyAnalysisResult;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyInput;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use App\Tools\BusinessDocumentValidator\Domain\Data\Inconsistency;
use App\Tools\BusinessDocumentValidator\Domain\Data\StateRegistrationValidationResult;
use App\Tools\BusinessDocumentValidator\Domain\Enums\InconsistencySeverity;
use App\Tools\BusinessDocumentValidator\Domain\Enums\RegistryLookupStatus;

final class CompanyConsistencyAnalyzer
{
    public function invalidDocument(CompanyConsistencyInput $input): CompanyConsistencyAnalysisResult
    {
        return new CompanyConsistencyAnalysisResult(
            completed: false,
            message: 'A análise não foi realizada porque o CNPJ é inválido.',
            inconsistencies: [new Inconsistency(
                code: 'cnpj_invalid',
                severity: InconsistencySeverity::Error,
                title: 'CNPJ matematicamente inválido',
                message: 'O número informado não passou na validação dos dígitos verificadores.',
                recommendation: 'Corrija o CNPJ antes de consultar ou comparar dados cadastrais.',
                informedValue: $input->cnpj,
            )],
        );
    }

    public function analyze(
        CompanyConsistencyInput $input,
        CompanyRegistryLookupResult $lookup,
        ?StateRegistrationValidationResult $stateRegistrationResult = null,
    ): CompanyConsistencyAnalysisResult {
        if ($lookup->status !== RegistryLookupStatus::Found || $lookup->company === null) {
            $severity = $lookup->status === RegistryLookupStatus::Unavailable
                ? InconsistencySeverity::Information
                : InconsistencySeverity::Warning;

            return new CompanyConsistencyAnalysisResult(
                completed: false,
                message: 'A análise cadastral não pôde ser concluída.',
                inconsistencies: [new Inconsistency(
                    code: 'registry_lookup_incomplete',
                    severity: $severity,
                    title: 'Consulta cadastral não concluída',
                    message: $lookup->message,
                    recommendation: 'Tente novamente mais tarde e confirme os dados diretamente em uma fonte oficial antes de tomar decisões.',
                )],
            );
        }

        $company = $lookup->company;
        $items = [];

        if (! $this->isActiveStatus($company->registrationStatus)) {
            $items[] = new Inconsistency(
                code: 'registration_status_irregular',
                severity: InconsistencySeverity::Error,
                title: 'Situação cadastral irregular',
                message: 'A situação cadastral encontrada não indica uma empresa ativa.',
                recommendation: 'Interrompa o cadastro ou a operação até confirmar a regularidade do CNPJ em fonte oficial.',
                registryValue: $company->registrationStatus ?: 'Não informada',
            );
        }

        $this->compareText(
            items: $items,
            code: 'legal_name_mismatch',
            title: 'Razão social divergente',
            informed: $input->legalName,
            registry: $company->legalName,
            recommendation: 'Atualize a razão social informada para coincidir com o cadastro oficial.',
            severity: InconsistencySeverity::Warning,
        );

        $this->compareText(
            items: $items,
            code: 'trade_name_mismatch',
            title: 'Nome fantasia divergente',
            informed: $input->tradeName,
            registry: $company->tradeName,
            recommendation: 'Revise o nome fantasia ou mantenha o campo vazio quando ele não constar no cadastro oficial.',
            severity: InconsistencySeverity::Warning,
        );

        $this->compareText(
            items: $items,
            code: 'city_mismatch',
            title: 'Município divergente',
            informed: $input->city,
            registry: $company->city,
            recommendation: 'Confirme o endereço cadastral e corrija o município informado.',
            severity: InconsistencySeverity::Warning,
        );

        if ($input->state !== null && $company->state !== null && $input->state !== mb_strtoupper($company->state)) {
            $items[] = new Inconsistency(
                code: 'state_mismatch',
                severity: InconsistencySeverity::Error,
                title: 'UF divergente',
                message: 'A UF informada não coincide com o endereço cadastral do CNPJ.',
                recommendation: 'Revise a UF e confirme se o estabelecimento consultado é a matriz ou a filial correta.',
                informedValue: $input->state,
                registryValue: mb_strtoupper($company->state),
            );
        }

        if ($input->stateRegistration !== null && $stateRegistrationResult !== null) {
            if (! $stateRegistrationResult->supported) {
                $items[] = new Inconsistency(
                    code: 'state_registration_not_supported',
                    severity: InconsistencySeverity::Information,
                    title: 'IE não validada automaticamente',
                    message: $stateRegistrationResult->message,
                    recommendation: 'Confirme a Inscrição Estadual no portal da Secretaria da Fazenda da UF correspondente.',
                    informedValue: $input->stateRegistration,
                );
            } elseif (! $stateRegistrationResult->valid) {
                $items[] = new Inconsistency(
                    code: 'state_registration_invalid',
                    severity: InconsistencySeverity::Error,
                    title: 'Inscrição Estadual inválida',
                    message: $stateRegistrationResult->message,
                    recommendation: 'Corrija a IE ou confirme o número no cadastro estadual antes de prosseguir.',
                    informedValue: $stateRegistrationResult->formatted ?: $stateRegistrationResult->normalized,
                );
            }
        }

        if ($items === []) {
            $items[] = new Inconsistency(
                code: 'no_inconsistencies_found',
                severity: InconsistencySeverity::Information,
                title: 'Nenhuma inconsistência encontrada',
                message: 'Os dados informados coincidem com as informações disponíveis na consulta e nas regras locais aplicáveis.',
                recommendation: 'Mantenha o comprovante da conferência e repita a validação sempre que houver atualização cadastral.',
            );
        }

        return new CompanyConsistencyAnalysisResult(
            completed: true,
            message: 'Análise de inconsistências concluída.',
            inconsistencies: $items,
            company: $company,
        );
    }

    /** @param list<Inconsistency> $items */
    private function compareText(
        array &$items,
        string $code,
        string $title,
        ?string $informed,
        ?string $registry,
        string $recommendation,
        InconsistencySeverity $severity,
    ): void {
        if ($informed === null || $registry === null || $this->normalizeText($informed) === $this->normalizeText($registry)) {
            return;
        }

        $items[] = new Inconsistency(
            code: $code,
            severity: $severity,
            title: $title,
            message: 'O valor informado não coincide com o valor retornado pela consulta cadastral.',
            recommendation: $recommendation,
            informedValue: $informed,
            registryValue: $registry,
        );
    }

    private function isActiveStatus(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        return $this->normalizeText($status) === 'ATIVA';
    }

    private function normalizeText(string $value): string
    {
        $value = mb_strtoupper(trim($value));
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = $transliterated === false ? $value : $transliterated;

        return preg_replace('/[^A-Z0-9]+/', '', $value) ?? '';
    }
}
