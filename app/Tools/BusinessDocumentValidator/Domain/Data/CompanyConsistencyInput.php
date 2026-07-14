<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class CompanyConsistencyInput
{
    public function __construct(
        public string $cnpj,
        public ?string $legalName,
        public ?string $tradeName,
        public ?string $state,
        public ?string $city,
        public ?string $stateRegistration,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            cnpj: trim((string) ($data['analysis_cnpj'] ?? '')),
            legalName: self::nullableString($data['legal_name'] ?? null),
            tradeName: self::nullableString($data['trade_name'] ?? null),
            state: self::nullableUpperString($data['analysis_state'] ?? null),
            city: self::nullableString($data['city'] ?? null),
            stateRegistration: self::nullableString($data['analysis_state_registration'] ?? null),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private static function nullableUpperString(mixed $value): ?string
    {
        $normalized = self::nullableString($value);

        return $normalized === null ? null : mb_strtoupper($normalized);
    }
}
