<?php

declare(strict_types=1);

namespace App\Core\Temporary\Infrastructure;

use App\Core\Temporary\Contracts\TemporaryPayloadStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;

final readonly class CacheTemporaryPayloadStore implements TemporaryPayloadStore
{
    public function __construct(private Repository $cache) {}

    public function put(string $namespace, array $payload, string $ownerKey, int $ttlMinutes = 30): string
    {
        $token = Str::random(48);
        $this->cache->put($this->key($namespace, $token, $ownerKey), $payload, now()->addMinutes($ttlMinutes));

        return $token;
    }

    public function get(string $namespace, string $token, string $ownerKey): ?array
    {
        $payload = $this->cache->get($this->key($namespace, $token, $ownerKey));

        return is_array($payload) ? $payload : null;
    }

    public function forget(string $namespace, string $token, string $ownerKey): void
    {
        $this->cache->forget($this->key($namespace, $token, $ownerKey));
    }

    private function key(string $namespace, string $token, string $ownerKey): string
    {
        return 'temporary:'.hash('sha256', $namespace.'|'.$ownerKey.'|'.$token);
    }
}
