<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Data;

use App\Core\Money\Money;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use DateTimeImmutable;

final readonly class ContractDraft
{
    /** @param array<string, scalar|null> $specificTerms */
    public function __construct(
        public ContractType $type,
        public ContractParty $firstParty,
        public ContractParty $secondParty,
        public Money $amount,
        public string $paymentTerms,
        public string $jurisdictionCity,
        public string $jurisdictionState,
        public string $signingCity,
        public DateTimeImmutable $signingDate,
        public array $specificTerms,
        public ?string $additionalTerms = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'first_party_label' => $this->type->firstPartyLabel(),
            'second_party_label' => $this->type->secondPartyLabel(),
            'first_party' => $this->firstParty->toArray(),
            'second_party' => $this->secondParty->toArray(),
            'amount_minor' => $this->amount->minorAmount(),
            'amount_formatted' => $this->amount->formatPtBr(),
            'payment_terms' => $this->paymentTerms,
            'jurisdiction_city' => $this->jurisdictionCity,
            'jurisdiction_state' => $this->jurisdictionState,
            'signing_city' => $this->signingCity,
            'signing_date' => $this->signingDate->format('Y-m-d'),
            'specific_terms' => $this->specificTerms,
            'additional_terms' => $this->additionalTerms,
        ];
    }
}
