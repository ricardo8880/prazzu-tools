<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunFavorites;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;
use App\Tools\ContractGenerator\Tool;

final readonly class ManageContractHistory
{
    public function __construct(private ToolRunHistory $history, private ToolRunFavorites $favorites) {}

    /** @return list<ToolRunEntry> */
    public function recent(int $userId, int $limit = 6): array
    {
        return $this->history->recentSucceeded(Tool::SLUG, $userId, $limit);
    }

    public function paginate(int $userId, int $page = 1): ToolRunPage
    {
        return $this->history->paginateSucceeded(new ToolRunHistoryQuery(Tool::SLUG, $userId, $page, 15));
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
        return $this->favorites->toggleOwned(Tool::SLUG, $runId, $userId);
    }

    /** @return array{left: ToolRunEntry, right: ToolRunEntry, changed: bool, left_lines: list<string>, right_lines: list<string>} */
    public function compare(string $leftId, string $rightId, int $userId): array
    {
        $left = $this->owned($leftId, $userId);
        $right = $this->owned($rightId, $userId);
        $leftText = (string) ($left->result['contract_text'] ?? $left->result['details']['contract_text'] ?? '');
        $rightText = (string) ($right->result['contract_text'] ?? $right->result['details']['contract_text'] ?? '');

        return [
            'left' => $left,
            'right' => $right,
            'changed' => $leftText !== $rightText,
            'left_lines' => preg_split('/\R/u', $leftText) ?: [],
            'right_lines' => preg_split('/\R/u', $rightText) ?: [],
        ];
    }
}
