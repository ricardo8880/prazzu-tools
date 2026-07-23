<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class ReceiptNumber
{
    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalized = strtoupper(trim($value));

        if ($normalized === '' || mb_strlen($normalized) > 40) {
            throw new InvalidArgumentException('O número do recibo deve ter entre 1 e 40 caracteres.');
        }

        if (! preg_match('/^[A-Z0-9][A-Z0-9.\/_-]*$/', $normalized)) {
            throw new InvalidArgumentException('O número do recibo contém caracteres inválidos.');
        }

        return new self($normalized);
    }
}
