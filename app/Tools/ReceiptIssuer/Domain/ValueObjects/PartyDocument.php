<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\ValueObjects;

use App\Tools\ReceiptIssuer\Domain\Enums\DocumentType;
use InvalidArgumentException;

final readonly class PartyDocument
{
    private function __construct(public DocumentType $type, private string $value) {}

    public static function ein(string $value): self
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if (! preg_match('/^\d{9}$/', $digits)) {
            throw new InvalidArgumentException('EIN must contain exactly nine digits.');
        }

        return new self(DocumentType::Ein, $digits);
    }

    public static function id(string $value): self
    {
        $value = trim($value);
        if (mb_strlen($value) < 2 || mb_strlen($value) > 40) {
            throw new InvalidArgumentException('Reference ID must contain between 2 and 40 characters.');
        }

        return new self(DocumentType::Id, $value);
    }

    public function digits(): string
    {
        return $this->type === DocumentType::Ein ? $this->value : (preg_replace('/\D+/', '', $this->value) ?? '');
    }

    public function formatted(): string
    {
        if ($this->type === DocumentType::Ein) {
            return substr($this->value, 0, 2).'-'.substr($this->value, 2);
        }

        return $this->value;
    }

    public function masked(): string
    {
        if ($this->type === DocumentType::Ein) {
            return '**-***'.substr($this->value, -4);
        }

        return mb_strlen($this->value) <= 4 ? str_repeat('*', mb_strlen($this->value)) : str_repeat('*', mb_strlen($this->value) - 4).substr($this->value, -4);
    }
}
