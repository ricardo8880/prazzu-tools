<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Unit;

use App\Core\Money\BrazilianMoneyInWords;
use App\Tools\ContractGenerator\Application\Actions\BuildContractDraft;
use App\Tools\ContractGenerator\Domain\Services\ContractTextGenerator;
use PHPUnit\Framework\TestCase;

final class ContractTextGeneratorTest extends TestCase
{
    public function test_generates_complete_service_contract_text(): void
    {
        $draft = (new BuildContractDraft())->execute($this->servicePayload());
        $contract = (new ContractTextGenerator(new BrazilianMoneyInWords()))->generate($draft);

        self::assertSame('CONTRATO PARTICULAR DE PRESTAÇÃO DE SERVIÇOS', $contract->title);
        self::assertStringContainsString('CONTRATANTE: Empresa Contratante Ltda.', $contract->content);
        self::assertStringContainsString('R$ 2.500,00 (dois mil e quinhentos reais)', $contract->content);
        self::assertStringContainsString('CLÁUSULA 1ª — DO OBJETO', $contract->content);
        self::assertStringContainsString('Consultoria contábil mensal.', $contract->content);
        self::assertStringContainsString('30 dias', $contract->content);
        self::assertStringContainsString('São Paulo/SP', $contract->content);
    }

    public function test_generates_complete_movable_asset_sale_contract_text(): void
    {
        $payload = $this->servicePayload();
        $payload['contract_type'] = 'compra-venda-bem-movel';
        unset($payload['service_description'], $payload['start_date'], $payload['end_date'], $payload['termination_notice_days']);
        $payload['asset_description'] = 'Notebook empresarial, número de série ABC123.';
        $payload['delivery_date'] = '2026-08-10';
        $payload['delivery_location'] = 'São Paulo/SP';

        $draft = (new BuildContractDraft())->execute($payload);
        $contract = (new ContractTextGenerator(new BrazilianMoneyInWords()))->generate($draft);

        self::assertSame('CONTRATO PARTICULAR DE COMPRA E VENDA DE BEM MÓVEL', $contract->title);
        self::assertStringContainsString('VENDEDOR: Empresa Contratante Ltda.', $contract->content);
        self::assertStringContainsString('COMPRADOR: Maria da Silva', $contract->content);
        self::assertStringContainsString('Notebook empresarial, número de série ABC123.', $contract->content);
        self::assertStringContainsString('10 de agosto de 2026', $contract->content);
        self::assertStringContainsString('CLÁUSULA FINAL — DO FORO', $contract->content);
    }

    /** @return array<string, mixed> */
    private function servicePayload(): array
    {
        return [
            'contract_type' => 'prestacao-servicos',
            'first_party_name' => 'Empresa Contratante Ltda.',
            'first_party_document_type' => 'cnpj',
            'first_party_document' => '04.252.011/0001-10',
            'first_party_address' => 'Avenida Paulista, 1000',
            'first_party_city' => 'São Paulo',
            'first_party_state' => 'SP',
            'second_party_name' => 'Maria da Silva',
            'second_party_document_type' => 'cpf',
            'second_party_document' => '529.982.247-25',
            'second_party_address' => 'Rua das Flores, 100',
            'second_party_city' => 'Campinas',
            'second_party_state' => 'SP',
            'amount' => '2.500,00',
            'payment_terms' => 'Pagamento mensal até o quinto dia útil.',
            'service_description' => 'Consultoria contábil mensal.',
            'start_date' => '2026-08-01',
            'end_date' => '2027-07-31',
            'termination_notice_days' => 30,
            'jurisdiction_city' => 'São Paulo',
            'jurisdiction_state' => 'SP',
            'signing_city' => 'São Paulo',
            'signing_date' => '2026-07-24',
            'additional_terms' => 'Reuniões poderão ocorrer por videoconferência.',
        ];
    }
}
