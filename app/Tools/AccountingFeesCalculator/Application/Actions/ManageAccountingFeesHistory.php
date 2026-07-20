<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;

final readonly class ManageAccountingFeesHistory
{
    public const TOOL_SLUG = 'calculadora-de-honorarios-contabeis';

    public const TYPE_CALCULATION = 'accounting_fee';

    public const TYPE_ADJUSTMENT = 'fee_adjustment';

    public const RULE_VERSION_CALCULATION = '1.0.0';

    public const RULE_VERSION_ADJUSTMENT = '1.0.0-fee-adjustment';

    public function __construct(
        private ToolRunHistory $history,
        private ToolRunFavorites $favorites,
    ) {}

    public function paginate(int $userId, string $type, int $page = 1, bool $favoritesOnly = false, int $perPage = 12): ToolRunPage
    {
        return $this->history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: self::TOOL_SLUG,
            userId: $userId,
            page: $page,
            perPage: $perPage,
            favoritesOnly: $favoritesOnly,
            ruleVersions: [$this->ruleVersionFor($type)],
        ));
    }

    public function owned(string $runId, int $userId, ?string $expectedType = null): ToolRunEntry
    {
        $run = $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);

        if ($expectedType !== null && data_get($run->input, 'run_type') !== $expectedType) {
            abort(404);
        }

        return $run;
    }

    public function delete(string $runId, int $userId, ?string $expectedType = null): void
    {
        $this->owned($runId, $userId, $expectedType);
        $this->history->deleteSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }

    public function toggleFavorite(string $runId, int $userId): bool
    {
        $this->owned($runId, $userId, self::TYPE_CALCULATION);

        return $this->favorites->toggleOwned(self::TOOL_SLUG, $runId, $userId);
    }

    private function ruleVersionFor(string $type): string
    {
        return match ($type) {
            self::TYPE_CALCULATION => self::RULE_VERSION_CALCULATION,
            self::TYPE_ADJUSTMENT => self::RULE_VERSION_ADJUSTMENT,
            default => abort(404),
        };
    }

    /** @return list<ToolRunEntry> */
    public function allCalculations(int $userId): array
    {
        return $this->history->recentSucceeded(self::TOOL_SLUG, $userId, 100);
    }
}
