<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\ReceiptIssuer\Domain\Data\ReceiptParty;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public ReceiptIdentifier $identifier,
        public ReceiptNumber $number,
        public ReceiptParty $payer,
        public ReceiptParty $payee,
        public Money $amount,
        public string $description,
        public DateTimeImmutable $issuedAt,
        public ?string $city = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier->value,
            'number' => $this->number->value,
            'payer' => $this->payer->toArray(),
            'payee' => $this->payee->toArray(),
            'amount_minor' => $this->amount->minorAmount(),
            'description' => trim($this->description),
            'issued_at' => $this->issuedAt->format('Y-m-d'),
            'city' => $this->city === null ? null : trim($this->city),
        ];
    }
}
