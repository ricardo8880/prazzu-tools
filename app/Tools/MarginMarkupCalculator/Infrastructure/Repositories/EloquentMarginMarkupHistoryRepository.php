<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Infrastructure\Repositories;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;

final readonly class EloquentMarginMarkupHistoryRepository
{
    private const TOOL_SLUG = 'calculadora-margem-markup';

    public function __construct(private ToolRunHistory $history) {}

    public function findOwned(string $runId, int $userId): ToolRunEntry
    {
        return $this->history->findSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
