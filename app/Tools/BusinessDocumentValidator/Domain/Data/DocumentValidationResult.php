<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

use App\Tools\BusinessDocumentValidator\Domain\Enums\DocumentType;

final readonly class DocumentValidationResult
{
    /** @param list<string> $messages */
    public function __construct(
        public DocumentType $type,
        public string $digits,
        public string $formatted,
        public bool $valid,
        public bool $automaticallyDetected,
        public array $messages,
    ) {}

    /** @return array<string, bool|string|list<string>> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'digits' => $this->digits,
            'formatted' => $this->formatted,
            'valid' => $this->valid,
            'automatically_detected' => $this->automaticallyDetected,
            'messages' => $this->messages,
        ];
    }
}
