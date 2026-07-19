<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Tools\BusinessDocumentValidator\Domain\Analyzers\CompanyConsistencyAnalyzer;
use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyAnalysisResult;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyConsistencyInput;
use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use App\Tools\BusinessDocumentValidator\Domain\Validators\StateRegistrationValidatorRegistry;

final readonly class AnalyzeCompanyConsistency
{
    public function __construct(
        private BusinessDocumentValidator $documentValidator,
        private CompanyRegistryProvider $provider,
        private StateRegistrationValidatorRegistry $stateRegistrationValidators,
        private CompanyConsistencyAnalyzer $analyzer,
    ) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): CompanyConsistencyAnalysisResult
    {
        $input = CompanyConsistencyInput::fromArray($data);
        $document = $this->documentValidator->validate($input->cnpj, DocumentType::Cnpj);

        if (! $document->valid) {
            return $this->analyzer->invalidDocument($input);
        }

        $lookup = $this->provider->lookup($document->digits);
        $stateRegistrationResult = null;

        if ($input->stateRegistration !== null && $input->state !== null) {
            $stateRegistrationResult = $this->stateRegistrationValidators->validate(
                $input->stateRegistration,
                $input->state,
            );
        }

        return $this->analyzer->analyze($input, $lookup, $stateRegistrationResult);
    }
}
