<?php

declare(strict_types=1);

namespace App\Core\Temporary\Contracts;

interface TemporaryPayloadStore
{
    /** @param array<string, mixed> $payload */
    public function put(string $namespace, array $payload, string $ownerKey, int $ttlMinutes = 30): string;

    /** @return array<string, mixed>|null */
    public function get(string $namespace, string $token, string $ownerKey): ?array;

    public function forget(string $namespace, string $token, string $ownerKey): void;
}
