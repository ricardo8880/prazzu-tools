<?php

declare(strict_types=1);

namespace App\Core\Documents\Data;

final readonly class GeneratedDocumentNotice
{
    /** @param list<string> $limitations */
    public function __construct(
        public string $purpose,
        public array $limitations,
        public bool $requiresReview = true,
        public bool $requiresSignature = true,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'purpose' => $this->purpose,
            'limitations' => $this->limitations,
            'requires_review' => $this->requiresReview,
            'requires_signature' => $this->requiresSignature,
            'authenticity_validated' => false,
        ];
    }
}
