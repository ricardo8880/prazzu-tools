<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class ReceiptIdentifier
{
    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));

        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized)) {
            throw new InvalidArgumentException('O identificador do recibo deve ser um UUID válido.');
        }

        return new self($normalized);
    }
}
