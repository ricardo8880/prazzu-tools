<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;
use App\Tools\FederalPaymentGuideGenerator\Tool;

final readonly class ManageGuideHistory
{
    public function __construct(
        private ToolRunHistory $history,
        private ToolRunFavorites $favorites,
    ) {}

    public function paginate(int $userId, int $page = 1, bool $favoritesOnly = false, int $perPage = 12): ToolRunPage
    {
        return $this->history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: Tool::SLUG,
            userId: $userId,
            page: $page,
            perPage: $perPage,
            favoritesOnly: $favoritesOnly,
            ruleVersions: [\App\Tools\FederalPaymentGuideGenerator\Domain\Rules\RuleCatalog::CURRENT_VERSION],
        ));
    }

    /** @return list<ToolRunEntry> */
    public function recent(int $userId, int $limit = 3): array
    {
        return $this->history->recentSucceeded(Tool::SLUG, $userId, $limit);
    }

    public function owned(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(Tool::SLUG, $runId, $userId);
    }

    public function delete(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(Tool::SLUG, $runId, $userId);
    }

    public function toggleFavorite(string $runId, int $userId): bool
    {
        $this->owned($runId, $userId);

        return $this->favorites->toggleOwned(Tool::SLUG, $runId, $userId);
    }
}
