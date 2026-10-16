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
            throw new InvalidArgumentException('Receipt number must contain 1 to 40 characters.');
        }

        if (! preg_match('/^[A-Z0-9][A-Z0-9.\/_-]*$/', $normalized)) {
            throw new InvalidArgumentException('Receipt number contains invalid characters.');
        }

        return new self($normalized);
    }
}
