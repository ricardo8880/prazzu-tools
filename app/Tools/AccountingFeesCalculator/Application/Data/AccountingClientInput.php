<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Data;

use App\Core\Money\Money;

final readonly class AccountingClientInput
{
    private function __construct(
        public string $companyName,
        public ?string $document,
        public string $contactName,
        public ?string $email,
        public ?string $phone,
        public Money $monthlyFee,
        public string $proposalStatus,
        public string $contractStatus,
        public string $pipelineStatus,
        public ?string $notes,
    ) {}

    /** @param array<string, mixed> $input */
    public static function fromArray(array $input): self
    {
        return new self(
            companyName: trim((string) $input['company_name']),
            document: self::optionalString($input['document'] ?? null),
            contactName: trim((string) $input['contact_name']),
            email: self::optionalString($input['email'] ?? null, lowercase: true),
            phone: self::optionalString($input['phone'] ?? null),
            monthlyFee: Money::fromDecimal((string) $input['monthly_fee']),
            proposalStatus: (string) $input['proposal_status'],
            contractStatus: (string) $input['contract_status'],
            pipelineStatus: (string) $input['pipeline_status'],
            notes: self::optionalString($input['notes'] ?? null),
        );
    }

    /** @return array<string, int|string|null> */
    public function toPersistenceArray(): array
    {
        return [
            'company_name' => $this->companyName,
            'document' => $this->document,
            'contact_name' => $this->contactName,
            'email' => $this->email,
            'phone' => $this->phone,
            'monthly_fee_cents' => $this->monthlyFee->minorAmount(),
            'proposal_status' => $this->proposalStatus,
            'contract_status' => $this->contractStatus,
            'pipeline_status' => $this->pipelineStatus,
            'notes' => $this->notes,
        ];
    }

    private static function optionalString(mixed $value, bool $lowercase = false): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        if ($normalized === '') {
            return null;
        }

        return $lowercase ? mb_strtolower($normalized) : $normalized;
    }
}
