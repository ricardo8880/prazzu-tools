<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Infrastructure\Repositories;

use App\Tools\SimplesNacionalCalculator\Infrastructure\Models\SimplesNacionalCalculation;
use Illuminate\Support\Collection;

final class SimplesNacionalCalculationRepository
{
    /** @param array<string, mixed> $attributes */
    public function create(array $attributes): SimplesNacionalCalculation
    {
        return SimplesNacionalCalculation::query()->create($attributes);
    }

    /** @return Collection<int, SimplesNacionalCalculation> */
    public function recentForUser(int $userId, int $limit): Collection
    {
        return SimplesNacionalCalculation::query()
            ->where('user_id', $userId)
            ->latest('reference_month')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function deleteOwned(int $calculationId, int $userId): void
    {
        $calculation = SimplesNacionalCalculation::query()->findOrFail($calculationId);

        abort_unless((int) $calculation->user_id === $userId, 404);

        $calculation->delete();
    }
}
