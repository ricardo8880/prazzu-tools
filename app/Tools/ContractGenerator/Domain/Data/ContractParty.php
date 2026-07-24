<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Data;

use App\Tools\ContractGenerator\Domain\Enums\PartyDocumentType;

final readonly class ContractParty
{
    public function __construct(
        public string $name,
        public PartyDocumentType $documentType,
        public string $document,
        public string $address,
        public string $city,
        public string $state,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'document_type' => $this->documentType->value,
            'document' => $this->document,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
        ];
    }
}
