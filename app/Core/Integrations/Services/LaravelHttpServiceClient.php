<?php

namespace App\Core\Integrations\Services;

use App\Core\Integrations\Contracts\ExternalServiceClient;
use App\Core\Integrations\Data\IntegrationResponse;
use App\Core\Integrations\Exceptions\IntegrationFailure;
use Illuminate\Http\Client\Factory;
use Throwable;

final readonly class LaravelHttpServiceClient implements ExternalServiceClient
{
    public function __construct(
        private Factory $http,
        private int $timeoutSeconds = 10,
        private int $retries = 2,
    ) {}

    public function request(string $method, string $uri, array $options = []): IntegrationResponse
    {
        try {
            $response = $this->http
                ->timeout($this->timeoutSeconds)
                ->retry($this->retries, 200, throw: false)
                ->send($method, $uri, $options);
        } catch (Throwable $exception) {
            throw new IntegrationFailure('Falha ao comunicar com serviço externo.', previous: $exception);
        }

        return new IntegrationResponse(
            status: $response->status(),
            data: is_array($response->json()) ? $response->json() : [],
            headers: $response->headers(),
        );
    }
}
