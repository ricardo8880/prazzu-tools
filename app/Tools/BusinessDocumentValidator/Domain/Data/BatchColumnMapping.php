<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class BatchColumnMapping
{
    public function __construct(
        public string $document,
        public ?string $legalName = null,
        public ?string $tradeName = null,
        public ?string $state = null,
        public ?string $city = null,
        public ?string $stateRegistration = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            document: (string) ($data['document_column'] ?? ''),
            legalName: self::nullable($data['legal_name_column'] ?? null),
            tradeName: self::nullable($data['trade_name_column'] ?? null),
            state: self::nullable($data['state_column'] ?? null),
            city: self::nullable($data['city_column'] ?? null),
            stateRegistration: self::nullable($data['state_registration_column'] ?? null),
        );
    }

    private static function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
