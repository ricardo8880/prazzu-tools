<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Contracts;

interface ToolRunFavorites
{
    public function favoriteOwned(string $toolSlug, string $runId, int $userId): void;

    public function unfavoriteOwned(string $toolSlug, string $runId, int $userId): void;

    public function toggleOwned(string $toolSlug, string $runId, int $userId): bool;

    public function isFavoriteOwned(string $toolSlug, string $runId, int $userId): bool;
}
