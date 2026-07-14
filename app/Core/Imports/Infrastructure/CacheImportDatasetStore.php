<?php

declare(strict_types=1);

namespace App\Core\Imports\Infrastructure;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Data\TabularDataset;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;

final readonly class CacheImportDatasetStore implements ImportDatasetStore
{
    public function __construct(private Repository $cache) {}

    public function put(TabularDataset $dataset, string $ownerKey, int $ttlMinutes = 30): string
    {
        $token = Str::random(48);
        $this->cache->put($this->key($token, $ownerKey), $dataset->toArray(), now()->addMinutes($ttlMinutes));

        return $token;
    }

    public function get(string $token, string $ownerKey): ?TabularDataset
    {
        $payload = $this->cache->get($this->key($token, $ownerKey));

        return is_array($payload) ? TabularDataset::fromArray($payload) : null;
    }

    public function forget(string $token, string $ownerKey): void
    {
        $this->cache->forget($this->key($token, $ownerKey));
    }

    private function key(string $token, string $ownerKey): string
    {
        return 'imports:'.hash('sha256', $ownerKey.'|'.$token);
    }
}
