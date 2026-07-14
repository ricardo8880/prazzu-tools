<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

use App\Tools\BusinessDocumentValidator\Domain\Enums\InconsistencySeverity;

final readonly class Inconsistency
{
    public function __construct(
        public string $code,
        public InconsistencySeverity $severity,
        public string $title,
        public string $message,
        public string $recommendation,
        public ?string $informedValue = null,
        public ?string $registryValue = null,
    ) {
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'severity' => $this->severity->value,
            'severity_label' => $this->severity->label(),
            'title' => $this->title,
            'message' => $this->message,
            'recommendation' => $this->recommendation,
            'informed_value' => $this->informedValue,
            'registry_value' => $this->registryValue,
        ];
    }
}
