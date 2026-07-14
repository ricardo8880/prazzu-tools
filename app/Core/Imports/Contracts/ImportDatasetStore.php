<?php

declare(strict_types=1);

namespace App\Core\Imports\Contracts;

use App\Core\Imports\Data\TabularDataset;

interface ImportDatasetStore
{
    public function put(TabularDataset $dataset, string $ownerKey, int $ttlMinutes = 30): string;

    public function get(string $token, string $ownerKey): ?TabularDataset;

    public function forget(string $token, string $ownerKey): void;
}
