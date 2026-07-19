<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class CompanyRegistryData
{
    /**
     * @param list<CompanyActivity> $secondaryActivities
     */
    public function __construct(
        public string $cnpj,
        public string $legalName,
        public ?string $tradeName,
        public ?string $registrationStatus,
        public ?string $registrationStatusDate,
        public ?string $openingDate,
        public ?string $legalNature,
        public ?string $branchType,
        public ?CompanyActivity $primaryActivity,
        public array $secondaryActivities,
        public ?string $street,
        public ?string $number,
        public ?string $complement,
        public ?string $district,
        public ?string $city,
        public ?string $state,
        public ?string $postalCode,
        public string $source,
        public string $consultedAt,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'cnpj' => $this->cnpj,
            'legal_name' => $this->legalName,
            'trade_name' => $this->tradeName,
            'registration_status' => $this->registrationStatus,
            'registration_status_date' => $this->registrationStatusDate,
            'opening_date' => $this->openingDate,
            'legal_nature' => $this->legalNature,
            'branch_type' => $this->branchType,
            'primary_activity' => $this->primaryActivity?->toArray(),
            'secondary_activities' => array_map(
                static fn (CompanyActivity $activity): array => $activity->toArray(),
                $this->secondaryActivities,
            ),
            'address' => [
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'district' => $this->district,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postalCode,
            ],
            'source' => $this->source,
            'consulted_at' => $this->consultedAt,
        ];
    }
}
