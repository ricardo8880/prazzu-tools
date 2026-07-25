<?php

declare(strict_types=1);

namespace App\Core\Imports\Infrastructure;

use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Data\TabularDataset;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Str;
use JsonException;

final readonly class CacheImportDatasetStore implements ImportDatasetStore
{
    public function __construct(
        private Repository $cache,
        private Encrypter $encrypter,
    ) {}

    public function put(TabularDataset $dataset, string $ownerKey, int $ttlMinutes = 30): string
    {
        $token = Str::random(48);
        $payload = json_encode($dataset->toArray(), JSON_THROW_ON_ERROR);
        $this->cache->put(
            $this->key($token, $ownerKey),
            $this->encrypter->encryptString($payload),
            now()->addMinutes($ttlMinutes),
        );

        return $token;
    }

    public function get(string $token, string $ownerKey): ?TabularDataset
    {
        $encrypted = $this->cache->get($this->key($token, $ownerKey));
        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        try {
            $payload = json_decode($this->encrypter->decryptString($encrypted), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

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
