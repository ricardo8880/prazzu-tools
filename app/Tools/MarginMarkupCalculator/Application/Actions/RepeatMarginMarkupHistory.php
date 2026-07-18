<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;

final readonly class RepeatMarginMarkupHistory
{
    public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}

    /** @return array<string, mixed> */
    public function execute(ToolRun $run, int $userId): array
    {
        return $this->ownedRun->execute($run, $userId)->input_payload ?? [];
    }
}
