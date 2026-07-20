<?php

namespace Tests\Unit\Core\ToolIntegration;

use App\Core\ToolIntegration\Data\IntegrationContract;
use App\Core\ToolIntegration\Data\IntegrationField;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\ToolIntegration\Exceptions\IntegrationContractNotFound;
use App\Core\ToolIntegration\Exceptions\InvalidIntegrationPayload;
use App\Core\ToolIntegration\Services\DefaultToolResultExchange;
use App\Core\ToolIntegration\Services\InMemoryToolIntegrationCatalog;
use App\Core\ToolIntegration\Services\IntegrationPayloadValidator;
use App\Core\ToolIntegration\Services\RequestToolResultStore;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use PHPUnit\Framework\TestCase;

final class ToolResultExchangeTest extends TestCase
{
    public function test_it_publishes_and_resolves_a_valid_payload(): void
    {
        $catalog = new InMemoryToolIntegrationCatalog;
        $catalog->register(new IntegrationContract(
            name: 'company-tax-snapshot',
            version: 1,
            description: 'Resumo tributário da empresa.',
            fields: [
                new IntegrationField('revenue', 'float', true),
                new IntegrationField('annex', 'string'),
            ],
        ));

        $exchange = new DefaultToolResultExchange(
            $catalog,
            new RequestToolResultStore($this->session()),
            new IntegrationPayloadValidator,
        );

        $payload = new IntegrationPayload(
            sourceTool: 'simples-nacional-calculator',
            contractName: 'company-tax-snapshot',
            contractVersion: 1,
            data: ['revenue' => 120000.0, 'annex' => 'III'],
        );

        $exchange->publish($payload);

        self::assertEquals($payload, $exchange->latest('company-tax-snapshot', 1));
    }

    public function test_it_rejects_an_unregistered_contract(): void
    {
        $exchange = new DefaultToolResultExchange(
            new InMemoryToolIntegrationCatalog,
            new RequestToolResultStore($this->session()),
            new IntegrationPayloadValidator,
        );

        $this->expectException(IntegrationContractNotFound::class);

        $exchange->publish(new IntegrationPayload(
            sourceTool: 'simples-nacional-calculator',
            contractName: 'company-tax-snapshot',
            contractVersion: 1,
            data: [],
        ));
    }

    public function test_it_rejects_invalid_or_unknown_fields(): void
    {
        $catalog = new InMemoryToolIntegrationCatalog;
        $catalog->register(new IntegrationContract(
            name: 'company-profile',
            version: 1,
            description: 'Perfil cadastral da empresa.',
            fields: [new IntegrationField('cnpj', 'string', true)],
        ));

        $exchange = new DefaultToolResultExchange(
            $catalog,
            new RequestToolResultStore($this->session()),
            new IntegrationPayloadValidator,
        );

        $this->expectException(InvalidIntegrationPayload::class);

        $exchange->publish(new IntegrationPayload(
            sourceTool: 'business-document-validator',
            contractName: 'company-profile',
            contractVersion: 1,
            data: ['cnpj' => 123, 'unexpected' => true],
        ));
    }

    private function session(): Store
    {
        return new Store('tool-integration-test', new ArraySessionHandler(120));
    }
}
