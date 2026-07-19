<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

use App\Tools\BusinessDocumentValidator\Domain\Enums\RegistryLookupStatus;

final readonly class CompanyRegistryLookupResult
{
    private function __construct(
        public RegistryLookupStatus $status,
        public ?CompanyRegistryData $company,
        public string $message,
    ) {}

    public static function found(CompanyRegistryData $company): self
    {
        return new self(RegistryLookupStatus::Found, $company, 'Dados cadastrais localizados com sucesso.');
    }

    public static function notFound(string $message = 'O CNPJ não foi localizado pelo provedor cadastral.'): self
    {
        return new self(RegistryLookupStatus::NotFound, null, $message);
    }

    public static function unavailable(string $message = 'O serviço de consulta cadastral está temporariamente indisponível.'): self
    {
        return new self(RegistryLookupStatus::Unavailable, null, $message);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'message' => $this->message,
            'company' => $this->company?->toArray(),
        ];
    }
}
