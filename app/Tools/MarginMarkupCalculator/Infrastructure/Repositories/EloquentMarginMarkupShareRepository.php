<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Infrastructure\Repositories;

use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use DateTimeInterface;

final class EloquentMarginMarkupShareRepository
{
    public function activeForRunAndUser(string $toolRunId, int $userId): ?MarginMarkupShare
    {
        return MarginMarkupShare::query()
            ->where('tool_run_id', $toolRunId)
            ->where('user_id', $userId)
            ->whereNull('revoked_at')
            ->where(static function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function createOrReplaceActive(
        string $toolRunId,
        int $userId,
        string $token,
        ?string $accessCodeHash,
        DateTimeInterface $expiresAt,
    ): MarginMarkupShare {
        return MarginMarkupShare::query()->updateOrCreate(
            [
                'tool_run_id' => $toolRunId,
                'user_id' => $userId,
                'revoked_at' => null,
            ],
            [
                'token' => $token,
                'access_code_hash' => $accessCodeHash,
                'expires_at' => $expiresAt,
            ],
        );
    }

    public function revokeActive(
        string $toolRunId,
        int $userId,
        DateTimeInterface $revokedAt,
    ): void {
        MarginMarkupShare::query()
            ->where('tool_run_id', $toolRunId)
            ->where('user_id', $userId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => $revokedAt]);
    }

    public function findByTokenOrFail(string $token): MarginMarkupShare
    {
        return MarginMarkupShare::query()->where('token', $token)->firstOrFail();
    }
}
