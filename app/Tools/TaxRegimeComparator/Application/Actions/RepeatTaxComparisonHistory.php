<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

final readonly class RepeatTaxComparisonHistory
{
    public function __construct(private RequireOwnedTaxComparisonRun $ownedRun) {}

    /** @return array<string, mixed> */
    public function execute(string $runId, int $userId): array
    {
        return $this->ownedRun->execute($runId, $userId)->input;
    }
}
