<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Application\Actions;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use App\Core\Money\Money;
use App\Tools\ContractGenerator\Domain\Data\ContractDraft;
use App\Tools\ContractGenerator\Domain\Data\ContractParty;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;
use DateTimeImmutable;

final class BuildContractDraft
{
    /** @param array<string, mixed> $data */
    public function execute(array $data): ContractDraft
    {
        $type = ContractType::from((string) $data['contract_type']);

        return new ContractDraft(
            type: $type,
            firstParty: $this->party($data, 'first_party'),
            secondParty: $this->party($data, 'second_party'),
            amount: Money::fromDecimal((string) $data['amount']),
            paymentTerms: trim((string) $data['payment_terms']),
            jurisdictionCity: trim((string) $data['jurisdiction_city']),
            jurisdictionState: strtoupper((string) $data['jurisdiction_state']),
            signingCity: trim((string) $data['signing_city']),
            signingDate: new DateTimeImmutable((string) $data['signing_date']),
            specificTerms: $this->specificTerms($type, $data),
            additionalTerms: filled($data['additional_terms'] ?? null) ? trim((string) $data['additional_terms']) : null,
        );
    }

    /** @param array<string, mixed> $data */
    private function party(array $data, string $prefix): ContractParty
    {
        $documentType = PartyDocumentType::from((string) $data[$prefix.'_document_type']);
        $document = (string) $data[$prefix.'_document'];

        $formattedDocument = match ($documentType) {
            PartyDocumentType::Cpf => Cpf::fromString($document)->formatted(),
            PartyDocumentType::Cnpj => Cnpj::fromString($document)->formatted(),
        };

        return new ContractParty(
            name: trim((string) $data[$prefix.'_name']),
            documentType: $documentType,
            document: $formattedDocument,
            address: trim((string) $data[$prefix.'_address']),
            city: trim((string) $data[$prefix.'_city']),
            state: strtoupper((string) $data[$prefix.'_state']),
        );
    }

    /** @param array<string, mixed> $data
     *  @return array<string, scalar|null>
     */
    private function specificTerms(ContractType $type, array $data): array
    {
        return match ($type) {
            ContractType::ServiceProvision => [
                'service_description' => trim((string) $data['service_description']),
                'start_date' => (string) $data['start_date'],
                'end_date' => filled($data['end_date'] ?? null) ? (string) $data['end_date'] : null,
                'termination_notice_days' => (int) $data['termination_notice_days'],
            ],
            ContractType::MovableAssetSale => [
                'asset_description' => trim((string) $data['asset_description']),
                'delivery_date' => (string) $data['delivery_date'],
                'delivery_location' => trim((string) $data['delivery_location']),
            ],
        };
    }
}
