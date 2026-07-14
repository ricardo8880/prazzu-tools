<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Tools\BusinessDocumentValidator\Domain\Analyzers\CompanyConsistencyAnalyzer;
use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\BatchColumnMapping;
use App\Tools\BusinessDocumentValidator\Domain\Data\BatchValidationResult;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyInput;
use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;
use App\Tools\BusinessDocumentValidator\Domain\Enums\RegistryLookupStatus;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrationValidatorRegistry;
use RuntimeException;

final readonly class ProcessBatchValidation
{
    private const MAXIMUM_REGISTRY_LOOKUPS = 50;

    public function __construct(
        private ImportDatasetStore $store,
        private BusinessDocumentValidator $documentValidator,
        private CompanyRegistryProvider $registryProvider,
        private CompanyConsistencyAnalyzer $consistencyAnalyzer,
        private StateRegistrationValidatorRegistry $stateRegistrationValidators,
    ) {}

    public function execute(array $data, string $ownerKey): BatchValidationResult
    {
        $token = (string) ($data['import_token'] ?? '');
        $dataset = $this->store->get($token, $ownerKey);
        if ($dataset === null) {
            throw new RuntimeException('A pré-visualização expirou. Importe o arquivo novamente.');
        }

        $mapping = BatchColumnMapping::fromArray($data);
        $consultRegistry = (bool) ($data['consult_registry'] ?? false);
        $seen = [];
        $results = [];
        $valid = $invalid = $duplicates = $withInconsistencies = $registryConsulted = $registryUnavailable = 0;

        foreach ($dataset->rows as $index => $row) {
            $document = (string) ($row[$mapping->document] ?? '');
            $validation = $this->documentValidator->validate($document, DocumentType::Automatic);
            $normalized = $validation->digits;
            $duplicate = $normalized !== '' && isset($seen[$normalized]);
            if ($normalized !== '') {
                $seen[$normalized] = true;
            }

            $validation->valid ? $valid++ : $invalid++;
            if ($duplicate) {
                $duplicates++;
            }

            $registry = null;
            $issues = [];
            if ($consultRegistry && $validation->valid && $validation->type->value === 'cnpj' && $registryConsulted < self::MAXIMUM_REGISTRY_LOOKUPS) {
                $registry = $this->registryProvider->lookup($normalized);
                $registryConsulted++;
                if ($registry->status === RegistryLookupStatus::Unavailable) {
                    $registryUnavailable++;
                }

                $input = new CompanyConsistencyInput(
                    cnpj: $document,
                    legalName: $this->cell($row, $mapping->legalName),
                    tradeName: $this->cell($row, $mapping->tradeName),
                    state: $this->upperCell($row, $mapping->state),
                    city: $this->cell($row, $mapping->city),
                    stateRegistration: $this->cell($row, $mapping->stateRegistration),
                );
                $stateRegistration = $input->state !== null && $input->stateRegistration !== null
                    ? $this->stateRegistrationValidators->validate($input->stateRegistration, $input->state)
                    : null;
                $analysis = $this->consistencyAnalyzer->analyze($input, $registry, $stateRegistration);
                $issues = array_map(static fn ($issue): array => $issue->toArray(), $analysis->inconsistencies);
                if (array_filter($issues, static fn (array $issue): bool => in_array($issue['severity'], ['error', 'warning'], true)) !== []) {
                    $withInconsistencies++;
                }
            }

            $results[] = [
                'line' => $index + 2,
                'document' => $document,
                'formatted_document' => $validation->formatted,
                'type' => $validation->type->label(),
                'valid' => $validation->valid,
                'message' => implode(' ', $validation->messages),
                'duplicate' => $duplicate,
                'registry_status' => $registry?->status->value,
                'registry_message' => $registry?->message,
                'company' => $registry?->company?->toArray(),
                'inconsistencies' => $issues,
            ];
        }

        return new BatchValidationResult($results, $valid, $invalid, $duplicates, $withInconsistencies, $registryConsulted, $registryUnavailable);
    }

    private function upperCell(array $row, ?string $column): ?string
    {
        $value = $this->cell($row, $column);

        return $value === null ? null : mb_strtoupper($value);
    }

    private function cell(array $row, ?string $column): ?string
    {
        if ($column === null) {
            return null;
        }

        $value = trim((string) ($row[$column] ?? ''));

        return $value === '' ? null : $value;
    }
}
