<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Repositories;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class AccountingFeeCalculationRepository
{
    /** @param array<string, mixed> $input @param array<string, mixed> $result */
    public function store(AccountingFeesOwner $owner, array $input, array $result): void
    {
        AccountingFeeCalculation::query()->create([
            'user_id' => $owner->userId,
            'session_key' => $owner->userId === null ? $owner->sessionKey : null,
            'input' => $input,
            'result' => $result,
        ]);
    }

    /** @return LengthAwarePaginator<AccountingFeeCalculation> */
    public function paginate(AccountingFeesOwner $owner, bool $favorite, int $perPage = 12): LengthAwarePaginator
    {
        return AccountingFeeCalculation::query()
            ->visibleTo($owner->userId, $owner->sessionKey)
            ->when($favorite, fn ($query) => $query->where('is_favorite', true))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return Collection<int, AccountingFeeCalculation> */
    public function all(AccountingFeesOwner $owner): Collection
    {
        return AccountingFeeCalculation::query()
            ->visibleTo($owner->userId, $owner->sessionKey)
            ->latest()
            ->get();
    }

    public function toggleFavorite(AccountingFeeCalculation $calculation): bool
    {
        $favorite = ! $calculation->is_favorite;
        $calculation->update(['is_favorite' => $favorite]);

        return $favorite;
    }

    public function delete(AccountingFeeCalculation $calculation): void
    {
        $calculation->delete();
    }
}
