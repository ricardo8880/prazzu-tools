<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupShareRepository;

final readonly class RevokeMarginMarkupShare
{
    public function __construct(
        private RequireOwnedMarginMarkupRun $ownedRun,
        private EloquentMarginMarkupShareRepository $shares,
    ) {}

    public function execute(ToolRun $run, int $userId): void
    {
        $run = $this->ownedRun->execute($run, $userId);

        $this->shares->revokeActive(
            (string) $run->getKey(),
            $userId,
            now(),
        );
    }
}
