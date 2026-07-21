<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Domain\Data;

final readonly class FiscalParty
{
    public function __construct(
        public string $name,
        public ?string $taxId,
        public ?string $stateRegistration,
    ) {}

    public function toArray(): array
    {
        return ['name' => $this->name, 'tax_id' => $this->taxId, 'state_registration' => $this->stateRegistration];
    }
}
