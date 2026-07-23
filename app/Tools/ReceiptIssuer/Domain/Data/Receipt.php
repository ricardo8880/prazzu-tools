<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Data;

use App\Core\Money\Money;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class Receipt
{
    public function __construct(
        public ReceiptIdentifier $identifier,
        public ReceiptNumber $number,
        public ReceiptParty $payer,
        public ReceiptParty $payee,
        public Money $amount,
        public string $amountInWords,
        public string $description,
        public DateTimeImmutable $issuedAt,
        public ?string $city = null,
    ) {
        if ($amount->minorAmount() <= 0) {
            throw new InvalidArgumentException('O valor do recibo deve ser maior que zero.');
        }

        $description = trim($this->description);
        if (mb_strlen($description) < 3 || mb_strlen($description) > 1000) {
            throw new InvalidArgumentException('A descrição deve ter entre 3 e 1.000 caracteres.');
        }

        if ($this->city !== null && (trim($this->city) === '' || mb_strlen(trim($this->city)) > 120)) {
            throw new InvalidArgumentException('A cidade deve ter entre 1 e 120 caracteres.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier->value,
            'number' => $this->number->value,
            'payer' => $this->payer->toArray(),
            'payee' => $this->payee->toArray(),
            'amount' => $this->amount->formatPtBr(),
            'amount_minor' => $this->amount->minorAmount(),
            'amount_in_words' => $this->amountInWords,
            'description' => trim($this->description),
            'issued_at' => $this->issuedAt->format('Y-m-d'),
            'city' => $this->city === null ? null : trim($this->city),
        ];
    }
}
