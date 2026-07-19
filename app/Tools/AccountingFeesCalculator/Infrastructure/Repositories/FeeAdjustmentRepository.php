<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Infrastructure\Repositories;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Domain\Data\FeeAdjustmentResult;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\FeeAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FeeAdjustmentRepository
{
    /** @return LengthAwarePaginator<FeeAdjustment> */
    public function paginate(AccountingFeesOwner $owner, int $perPage = 12): LengthAwarePaginator
    {
        return FeeAdjustment::query()
            ->visibleTo($owner->userId, $owner->sessionKey)
            ->latest()
            ->paginate($perPage);
    }

    /** @param array<string, mixed> $input */
    public function store(AccountingFeesOwner $owner, array $input, FeeAdjustmentResult $result): void
    {
        FeeAdjustment::query()->create([
            'user_id' => $owner->userId,
            'session_key' => $owner->userId === null ? $owner->sessionKey : null,
            'scenario_label' => trim((string) $input['scenario_label']),
            'index_type' => $input['index_type'],
            'reference_period' => $input['reference_period'],
            'percentage' => $result->percentage->toDecimalString(),
            'current_value_cents' => $result->currentValue->minorAmount(),
            'difference_cents' => $result->difference->minorAmount(),
            'adjusted_value_cents' => $result->adjustedValue->minorAmount(),
            'notes' => self::optionalString($input['notes'] ?? null),
        ]);
    }

    public function delete(FeeAdjustment $adjustment): void
    {
        $adjustment->delete();
    }

    private static function optionalString(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        return $normalized === '' ? null : $normalized;
    }
}
