<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Infrastructure\Repositories;

use App\Tools\AssetDepreciationCalculator\Infrastructure\Models\RegisteredAsset;
use Illuminate\Database\Eloquent\Collection;

final class RegisteredAssetRepository
{
    /** @return Collection<int, RegisteredAsset> */
    public function forUser(int $userId): Collection
    {
        return RegisteredAsset::query()->where('user_id', $userId)->orderBy('name')->get();
    }

    /** @param array{name: string, value_minor: int, useful_life_years: int, method: string} $attributes */
    public function createForUser(int $userId, array $attributes): RegisteredAsset
    {
        return RegisteredAsset::query()->create(['user_id' => $userId, ...$attributes]);
    }

    public function findForUser(int $assetId, int $userId): ?RegisteredAsset
    {
        return RegisteredAsset::query()->whereKey($assetId)->where('user_id', $userId)->first();
    }

    public function deleteForUser(RegisteredAsset $asset, int $userId): bool
    {
        if ((int) $asset->user_id !== $userId) {
            return false;
        }

        return (bool) $asset->delete();
    }
}
