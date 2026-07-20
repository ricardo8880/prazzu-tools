<?php

namespace App\Core\ToolIntegration\Services;

use App\Core\ToolIntegration\Contracts\ToolResultStore;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use DateTimeImmutable;
use Illuminate\Contracts\Session\Session;

final readonly class RequestToolResultStore implements ToolResultStore
{
    private const SESSION_KEY = 'tool_integration.payloads';

    public function __construct(private Session $session) {}

    public function put(IntegrationPayload $payload): void
    {
        $payloads = $this->session->get(self::SESSION_KEY, []);
        $payloads[$payload->contractKey()] = [
            'source_tool' => $payload->sourceTool,
            'contract_name' => $payload->contractName,
            'contract_version' => $payload->contractVersion,
            'data' => $payload->data,
            'created_at' => $payload->createdAt->format('Y-m-d\TH:i:s.uP'),
        ];
        $this->session->put(self::SESSION_KEY, $payloads);
    }

    public function latest(string $contractKey): ?IntegrationPayload
    {
        $stored = $this->session->get(self::SESSION_KEY.'.'.$contractKey);

        if (! is_array($stored)) {
            return null;
        }

        return new IntegrationPayload(
            sourceTool: (string) $stored['source_tool'],
            contractName: (string) $stored['contract_name'],
            contractVersion: (int) $stored['contract_version'],
            data: (array) $stored['data'],
            createdAt: new DateTimeImmutable((string) $stored['created_at']),
        );
    }
}
