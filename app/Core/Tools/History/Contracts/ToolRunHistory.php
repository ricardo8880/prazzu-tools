<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Contracts;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;

interface ToolRunHistory
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $result
     */
    public function recordSucceeded(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        array $result,
        int $userId,
    ): ToolRunEntry;

    /** @return list<ToolRunEntry> */
    public function recentSucceeded(string $toolSlug, int $userId, int $limit = 24): array;

    public function paginateSucceeded(ToolRunHistoryQuery $query): ToolRunPage;

    public function findSucceededOwned(string $toolSlug, string $runId, int $userId): ToolRunEntry;

    public function deleteSucceededOwned(string $toolSlug, string $runId, int $userId): void;
}
