<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupShareRepository;

final readonly class ShowMarginMarkupHistory
{
    public function __construct(
        private RequireOwnedMarginMarkupRun $ownedRun,
        private EloquentMarginMarkupShareRepository $shares,
    ) {}

    /** @return array{run: ToolRun, activeShare: MarginMarkupShare|null} */
    public function execute(ToolRun $run, int $userId): array
    {
        $run = $this->ownedRun->execute($run, $userId);

        return [
            'run' => $run,
            'activeShare' => $this->shares->activeForRunAndUser(
                (string) $run->getKey(),
                $userId,
            ),
        ];
    }
}
