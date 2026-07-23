<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Application\Actions;

use App\Core\Money\Money;
use App\Tools\ReceiptIssuer\Application\Data\CalculationInput;
use App\Tools\ReceiptIssuer\Domain\Data\ReceiptParty;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\PartyDocument;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;
use Illuminate\Support\Str;

final class BuildCalculationInput
{
    /** @param array<string, mixed> $data */
    public function execute(array $data): CalculationInput
    {
        return new CalculationInput(
            identifier: ReceiptIdentifier::fromString((string) Str::uuid()),
            number: ReceiptNumber::fromString((string) $data['number']),
            payer: new ReceiptParty(
                name: (string) $data['payer_name'],
                document: $this->document($data['payer_document_type'] ?? null, $data['payer_document'] ?? null),
            ),
            payee: new ReceiptParty(
                name: (string) $data['payee_name'],
                document: $this->document($data['payee_document_type'] ?? null, $data['payee_document'] ?? null),
            ),
            amount: Money::fromDecimal((string) $data['amount']),
            description: (string) $data['description'],
            issuedAt: new DateTimeImmutable((string) $data['issued_at']),
            city: filled($data['city'] ?? null) ? (string) $data['city'] : null,
        );
    }

    private function document(mixed $type, mixed $value): ?PartyDocument
    {
        if (! filled($value)) {
            return null;
        }

        return $type === 'cnpj'
            ? PartyDocument::cnpj((string) $value)
            : PartyDocument::cpf((string) $value);
    }
}
