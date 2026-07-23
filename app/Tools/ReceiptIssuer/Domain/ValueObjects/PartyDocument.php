<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\ValueObjects;

use App\Core\Identifiers\Cnpj;
use App\Core\Identifiers\Cpf;
use App\Tools\ReceiptIssuer\Domain\Enums\DocumentType;

final readonly class PartyDocument
{
    private function __construct(
        public DocumentType $type,
        private Cpf|Cnpj $document,
    ) {}

    public static function cpf(string $value): self
    {
        return new self(DocumentType::Cpf, Cpf::fromString($value));
    }

    public static function cnpj(string $value): self
    {
        return new self(DocumentType::Cnpj, Cnpj::fromString($value));
    }

    public function digits(): string
    {
        return $this->document->digits();
    }

    public function formatted(): string
    {
        return $this->document->formatted();
    }

    public function masked(): string
    {
        return $this->document->masked();
    }
}
