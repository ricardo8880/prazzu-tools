<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\ReceiptIssuer\Application\Data\CalculationInput;
use App\Tools\ReceiptIssuer\Domain\Data\ReceiptParty;
use App\Tools\ReceiptIssuer\Domain\Services\Calculator;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\PartyDocument;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptIdentifier;
use App\Tools\ReceiptIssuer\Domain\ValueObjects\ReceiptNumber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_issues_a_receipt_using_the_standard_result_contract(): void
    {
        $result = (new Calculator())->calculate(new CalculationInput(
            identifier: ReceiptIdentifier::fromString('550e8400-e29b-41d4-a716-446655440000'),
            number: ReceiptNumber::fromString('REC-2026-0001'),
            payer: new ReceiptParty('Empresa Pagadora Ltda.', PartyDocument::cnpj('11.222.333/0001-81')),
            payee: new ReceiptParty('Maria da Silva', PartyDocument::cpf('529.982.247-25')),
            amount: Money::fromDecimal('1.250,45'),
            description: 'Prestação de serviços contábeis referente a julho de 2026.',
            issuedAt: new DateTimeImmutable('2026-07-23'),
            city: 'São Paulo',
        ));

        self::assertSame('emissor-de-recibos', $result->toolSlug);
        self::assertSame('1.0.0', $result->schemaVersion);
        self::assertSame('REC-2026-0001', $result->summary[0]->value);
        self::assertSame('R$ 1.250,45', $result->summary[1]->value);
        self::assertSame('mil duzentos e cinquenta reais e quarenta e cinco centavos', $result->details['receipt']['amount_in_words']);
    }
}
