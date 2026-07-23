<?php

declare(strict_types=1);

namespace App\Integrations\PrazzuTools;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final readonly class PrazzuToolsClient
{
    public function __construct(
        private string $baseUrl,
        private string $token,
    ) {}

    /** @param array<string, mixed> $data */
    public function execute(string $tool, string $action, array $data): array
    {
        $response = $this->request()->post(
            sprintf('/api/v1/tools/%s/%s', $tool, $action),
            $data,
        );

        if (! $response->successful() || $response->json('success') !== true) {
            throw new RuntimeException(
                (string) ($response->json('error.message') ?? 'Falha ao executar a ferramenta.'),
            );
        }

        return (array) $response->json('data', []);
    }

    public function convertXml(string $absolutePath): array
    {
        $stream = fopen($absolutePath, 'rb');

        if ($stream === false) {
            throw new RuntimeException('Não foi possível abrir o XML informado.');
        }

        try {
            $response = $this->request()
                ->attach('xml_file', $stream, basename($absolutePath))
                ->post('/api/v1/tools/conversor-fiscal-xml/convert');
        } finally {
            fclose($stream);
        }

        if (! $response->successful() || $response->json('success') !== true) {
            throw new RuntimeException(
                (string) ($response->json('error.message') ?? 'Falha ao converter o XML.'),
            );
        }

        return (array) $response->json('data', []);
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl(rtrim($this->baseUrl, '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->timeout(30)
            ->retry(2, 200, throw: false);
    }
}
