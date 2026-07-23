<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\ReceiptIssuer\Domain\Data\ReceiptParty;
use App\Tools\ReceiptIssuer\Domain\Services\BrazilianMoneyInWords;
use App\Tools\ReceiptIssuer\Domain\Services\ReceiptIssuer;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\PartyDocument;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReceiptDomainValidationTest extends TestCase
{
    public function test_it_rejects_an_invalid_cpf(): void
    {
        $this->expectException(InvalidValue::class);
        PartyDocument::cpf('111.111.111-11');
    }

    public function test_it_rejects_an_invalid_receipt_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ReceiptNumber::fromString('recibo com espaços');
    }

    public function test_it_rejects_a_non_positive_amount(): void
    {
        $issuer = new ReceiptIssuer(new BrazilianMoneyInWords());

        $this->expectException(InvalidArgumentException::class);
        $issuer->issue(
            ReceiptIdentifier::fromString('550e8400-e29b-41d4-a716-446655440000'),
            ReceiptNumber::fromString('1'),
            new ReceiptParty('Pagador'),
            new ReceiptParty('Recebedor'),
            Money::zero(),
            'Pagamento de serviço.',
            new DateTimeImmutable('2026-07-23'),
        );
    }
}
