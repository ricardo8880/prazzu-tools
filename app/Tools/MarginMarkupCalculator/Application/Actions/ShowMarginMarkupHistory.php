<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;

final readonly class ShowMarginMarkupHistory
{
    public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}

    /** @return array{run: ToolRun} */
    public function execute(ToolRun $run, int $userId): array
    {
        return [
            'run' => $this->ownedRun->execute($run, $userId),
        ];
    }
}
