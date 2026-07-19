<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

final readonly class RepeatMarginMarkupHistory
{
    public function __construct(private RequireOwnedMarginMarkupRun $ownedRun) {}

    /** @return array<string,mixed> */
    public function execute(string $runId, int $userId): array
    {
        return $this->ownedRun->execute($runId, $userId)->input;
    }
}
