<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Unit;

use App\Tools\ReceiptIssuer\Application\Actions\BuildCalculationInput;
use PHPUnit\Framework\TestCase;

final class BuildCalculationInputTest extends TestCase
{
    public function test_it_builds_typed_input_from_form_data(): void
    {
        $input = (new BuildCalculationInput())->execute([
            'number' => 'rec-001',
            'payer_name' => 'Empresa Pagadora',
            'payer_document_type' => 'cnpj',
            'payer_document' => '04.252.011/0001-10',
            'payee_name' => 'Maria da Silva',
            'payee_document_type' => 'cpf',
            'payee_document' => '529.982.247-25',
            'amount' => '250,50',
            'description' => 'Prestação de serviços',
            'issued_at' => '2026-07-23',
            'city' => 'São Paulo',
        ]);

        self::assertSame('REC-001', $input->number->value);
        self::assertSame(25050, $input->amount->minorAmount());
        self::assertSame('04.252.011/0001-10', $input->payer->document?->formatted());
        self::assertSame('529.982.247-25', $input->payee->document?->formatted());
        self::assertSame('2026-07-23', $input->issuedAt->format('Y-m-d'));
    }
}
