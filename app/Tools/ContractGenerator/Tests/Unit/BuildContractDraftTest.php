<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Unit;

use App\Tools\ContractGenerator\Application\Actions\BuildContractDraft;
use App\Tools\ContractGenerator\Domain\Enums\ContractType;
use PHPUnit\Framework\TestCase;

final class BuildContractDraftTest extends TestCase
{
    public function test_builds_typed_service_contract_draft(): void
    {
        $draft = (new BuildContractDraft())->execute([
            'contract_type' => 'prestacao-servicos',
            'first_party_name' => 'Empresa Contratante Ltda.',
            'first_party_document_type' => 'cnpj',
            'first_party_document' => '04.252.011/0001-10',
            'first_party_address' => 'Avenida Paulista, 1000',
            'first_party_city' => 'São Paulo',
            'first_party_state' => 'sp',
            'second_party_name' => 'Maria da Silva',
            'second_party_document_type' => 'cpf',
            'second_party_document' => '529.982.247-25',
            'second_party_address' => 'Rua das Flores, 100',
            'second_party_city' => 'Campinas',
            'second_party_state' => 'sp',
            'amount' => '2.500,00',
            'payment_terms' => 'Pagamento mensal.',
            'service_description' => 'Consultoria contábil mensal.',
            'start_date' => '2026-08-01',
            'end_date' => null,
            'termination_notice_days' => 30,
            'jurisdiction_city' => 'São Paulo',
            'jurisdiction_state' => 'sp',
            'signing_city' => 'São Paulo',
            'signing_date' => '2026-07-24',
            'additional_terms' => null,
        ]);

        self::assertSame(ContractType::ServiceProvision, $draft->type);
        self::assertSame('04.252.011/0001-10', $draft->firstParty->document);
        self::assertSame('529.982.247-25', $draft->secondParty->document);
        self::assertSame('R$ 2.500,00', $draft->amount->formatPtBr());
        self::assertSame('SP', $draft->jurisdictionState);
        self::assertSame('Consultoria contábil mensal.', $draft->specificTerms['service_description']);
        self::assertNull($draft->specificTerms['end_date']);
    }
}
