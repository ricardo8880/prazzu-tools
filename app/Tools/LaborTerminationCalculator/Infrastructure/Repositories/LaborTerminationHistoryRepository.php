<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Infrastructure\Repositories;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;

final readonly class LaborTerminationHistoryRepository
{
    private const TOOL_SLUG = 'calculadora-de-rescisao';

    public function __construct(private ToolRunHistory $history) {}

    public function findOwned(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
