<?php

namespace App\Core\Tools\Api\Auth;

final class ApiClientRegistry
{
    public function authenticate(?string $plainTextToken): ?ApiClient
    {
        if ($plainTextToken === null || $plainTextToken === '') {
            return null;
        }

        foreach ($this->configuredClients() as $configuredClient) {
            $configuredToken = $configuredClient['token'] ?? null;

            if (! is_string($configuredToken) || $configuredToken === '') {
                continue;
            }

            if (! hash_equals($configuredToken, $plainTextToken)) {
                continue;
            }

            return new ApiClient(
                id: (string) $configuredClient['id'],
                name: (string) ($configuredClient['name'] ?? $configuredClient['id']),
                abilities: $this->normalizeAbilities($configuredClient['abilities'] ?? []),
            );
        }

        return null;
    }

    /** @return list<array{id: string, name?: string, token: string, abilities?: list<string>}> */
    private function configuredClients(): array
    {
        $clients = config('tools-api.clients', []);

        if (! is_array($clients)) {
            return [];
        }

        return array_values(array_filter($clients, static function (mixed $client): bool {
            return is_array($client)
                && isset($client['id'], $client['token'])
                && is_string($client['id'])
                && $client['id'] !== ''
                && is_string($client['token']);
        }));
    }

    /** @return list<string> */
    private function normalizeAbilities(mixed $abilities): array
    {
        if (! is_array($abilities)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            $abilities,
            static fn (mixed $ability): bool => is_string($ability) && $ability !== '',
        )));
    }
}
