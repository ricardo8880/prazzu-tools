<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Data;

use App\Tools\ReceiptIssuer\Domain\ValueObjects\PartyDocument;
use InvalidArgumentException;

final readonly class ReceiptParty
{
    public function __construct(
        public string $name,
        public ?PartyDocument $document = null,
    ) {
        $name = trim($this->name);

        if (mb_strlen($name) < 2 || mb_strlen($name) > 160) {
            throw new InvalidArgumentException('O nome deve ter entre 2 e 160 caracteres.');
        }
    }

    /** @return array{name: string, document_type: ?string, document: ?string} */
    public function toArray(): array
    {
        return [
            'name' => trim($this->name),
            'document_type' => $this->document?->type->value,
            'document' => $this->document?->formatted(),
        ];
    }
}
