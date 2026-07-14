<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Tools\BusinessDocumentValidator\Domain\Contracts\CompanyRegistryProvider;
use App\Tools\BusinessDocumentValidator\Domain\Data\CompanyRegistryLookupResult;
use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;

final readonly class LookupCompanyRegistry
{
    public function __construct(
        private BusinessDocumentValidator $validator,
        private CompanyRegistryProvider $provider,
    ) {
    }

    public function execute(string $document): CompanyRegistryLookupResult
    {
        $validation = $this->validator->validate($document, DocumentType::Cnpj);

        if (! $validation->valid) {
            return CompanyRegistryLookupResult::notFound(
                'A consulta não foi realizada porque o CNPJ informado é matematicamente inválido.',
            );
        }

        return $this->provider->lookup($validation->digits);
    }
}
