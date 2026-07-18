<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Models\ToolRun;
use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use App\Tools\MarginMarkupCalculator\Infrastructure\Repositories\EloquentMarginMarkupHistoryRepository;

final readonly class ShowSharedMarginMarkup
{
    public function __construct(
        private RequireAvailableMarginMarkupShare $availableShare,
        private EloquentMarginMarkupHistoryRepository $history,
    ) {}

    /** @return array{share: MarginMarkupShare, run: ToolRun|null, unlocked: bool} */
    public function execute(string $token, bool $sessionUnlocked): array
    {
        $share = $this->availableShare->execute($token);
        $unlocked = ! $share->isProtected() || $sessionUnlocked;

        return [
            'share' => $share,
            'run' => $unlocked
                ? $this->history->findOrFail((string) $share->tool_run_id)
                : null,
            'unlocked' => $unlocked,
        ];
    }
}
