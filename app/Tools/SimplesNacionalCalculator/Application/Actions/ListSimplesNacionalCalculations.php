<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Tools\SimplesNacionalCalculator\Tool;

final readonly class ListSimplesNacionalCalculations
{
    public function __construct(private ToolRunHistory $history) {}

    /** @return list<ToolRunEntry> */
    public function recent(?int $userId, int $limit = 24): array
    {
        if ($userId === null) {
            return [];
        }

        return $this->history->recentSucceeded(Tool::SLUG, $userId, $limit);
    }
}
