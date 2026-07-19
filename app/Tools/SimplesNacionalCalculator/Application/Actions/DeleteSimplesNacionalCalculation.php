<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Tools\SimplesNacionalCalculator\Tool;

final readonly class DeleteSimplesNacionalCalculation
{
    public function __construct(private ToolRunHistory $history) {}

    public function execute(string $calculationId, int $userId): void
    {
        $this->history->deleteSucceededOwned(Tool::SLUG, $calculationId, $userId);
    }
}
