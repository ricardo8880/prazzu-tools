<?php

namespace App\Core\ToolIntegration\Services;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\ToolIntegration\Contracts\ToolResultStore;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\ToolIntegration\Exceptions\IntegrationContractNotFound;

final readonly class DefaultToolResultExchange implements ToolResultPublisher, ToolResultResolver
{
    public function __construct(
        private ToolIntegrationCatalog $catalog,
        private ToolResultStore $store,
        private IntegrationPayloadValidator $validator,
    ) {
    }

    public function publish(IntegrationPayload $payload): void
    {
        $contract = $this->catalog->find($payload->contractName, $payload->contractVersion);

        if ($contract === null) {
            throw new IntegrationContractNotFound("O contrato [{$payload->contractKey()}] não está registrado.");
        }

        $this->validator->validate($payload, $contract);
        $this->store->put($payload);
    }

    public function latest(string $contractName, int $contractVersion): ?IntegrationPayload
    {
        return $this->store->latest("{$contractName}:v{$contractVersion}");
    }
}
