<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;

final readonly class DeleteTaxComparisonHistory
{
    private const TOOL_SLUG = 'comparador-tributario';

    public function __construct(private ToolRunHistory $history) {}

    public function execute(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
