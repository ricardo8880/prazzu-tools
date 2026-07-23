<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Money\Money;
use App\Tools\ReceiptIssuer\Domain\Data\Receipt;
use App\Tools\ReceiptIssuer\Domain\Data\ReceiptParty;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;

final readonly class ReceiptIssuer
{
    public function __construct(private BrazilianMoneyInWords $moneyInWords) {}

    public function issue(
        ReceiptIdentifier $identifier,
        ReceiptNumber $number,
        ReceiptParty $payer,
        ReceiptParty $payee,
        Money $amount,
        string $description,
        DateTimeImmutable $issuedAt,
        ?string $city = null,
    ): Receipt {
        return new Receipt(
            identifier: $identifier,
            number: $number,
            payer: $payer,
            payee: $payee,
            amount: $amount,
            amountInWords: $this->moneyInWords->convert($amount),
            description: $description,
            issuedAt: $issuedAt,
            city: $city,
        );
    }
}
