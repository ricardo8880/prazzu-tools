<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\History\Data\ToolRunHistoryQuery;
use App\Core\Tools\History\Data\ToolRunPage;
use App\Core\Tools\ToolRegistry;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class ToolHistoryManager
{
    public function __construct(
        private ToolRunHistory $history,
        private ToolRegistry $registry,
    ) {}

    /**
     * @param array<string, mixed> $input
     */
    public function record(
        string $toolSlug,
        array $input,
        ToolCalculationResult $result,
        int $userId,
        ?ReferenceDate $referenceDate = null,
        ?RuleVersion $ruleVersion = null,
    ): ToolRunEntry {
        $module = $this->requireHistoryModule($toolSlug);

        if ($result->toolSlug !== $toolSlug) {
            throw new InvalidArgumentException('O resultado não pertence à ferramenta informada.');
        }

        return $this->history->recordSucceeded(
            module: $module,
            ruleVersion: $ruleVersion ?? new RuleVersion($module->manifest()->version),
            referenceDate: $referenceDate ?? ReferenceDate::fromDateTime(new DateTimeImmutable('today')),
            input: $input,
            result: $result->toPersistenceArray(),
            userId: $userId,
        );
    }

    public function paginate(string $toolSlug, int $userId, int $page = 1, int $perPage = 15): ToolRunPage
    {
        $this->requireHistoryModule($toolSlug);

        return $this->history->paginateSucceeded(new ToolRunHistoryQuery(
            toolSlug: $toolSlug,
            userId: $userId,
            page: $page,
            perPage: $perPage,
        ));
    }

    public function find(string $toolSlug, string $runId, int $userId): ToolRunEntry
    {
        $this->requireHistoryModule($toolSlug);

        return $this->history->findSucceededOwned($toolSlug, $runId, $userId);
    }

    public function duplicate(string $toolSlug, string $runId, int $userId): ToolRunEntry
    {
        $module = $this->requireHistoryModule($toolSlug);
        $source = $this->history->findSucceededOwned($toolSlug, $runId, $userId);

        return $this->history->recordSucceeded(
            module: $module,
            ruleVersion: new RuleVersion($source->ruleVersion),
            referenceDate: ReferenceDate::fromString($source->referenceDate->format('Y-m-d')),
            input: $source->input,
            result: $source->result,
            userId: $userId,
        );
    }

    public function delete(string $toolSlug, string $runId, int $userId): void
    {
        $this->requireHistoryModule($toolSlug);
        $this->history->deleteSucceededOwned($toolSlug, $runId, $userId);
    }

    private function requireHistoryModule(string $toolSlug): HasHistoryPolicy
    {
        $module = $this->registry->findModule($toolSlug);

        if (! $module instanceof HasHistoryPolicy || ! $module->historyPolicy()->enabled) {
            throw new InvalidArgumentException('A ferramenta não possui histórico habilitado.');
        }

        return $module;
    }
}
