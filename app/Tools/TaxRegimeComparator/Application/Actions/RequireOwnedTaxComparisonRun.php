<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;

final readonly class RequireOwnedTaxComparisonRun
{
    private const TOOL_SLUG = 'comparador-tributario';

    public function __construct(private ToolRunHistory $history) {}

    public function execute(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
