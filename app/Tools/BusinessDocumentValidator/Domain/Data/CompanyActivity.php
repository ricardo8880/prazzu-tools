<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

final readonly class CompanyActivity
{
    public function __construct(
        public string $code,
        public string $description,
    ) {
    }

    /** @return array{code: string, description: string} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
        ];
    }
}
