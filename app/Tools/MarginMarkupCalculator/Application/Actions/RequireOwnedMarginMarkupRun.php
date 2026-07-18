<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class RequireOwnedMarginMarkupRun
{
    private const TOOL_SLUG = 'calculadora-margem-markup';

    public function execute(ToolRun $run, int $userId): ToolRun
    {
        if (
            (int) $run->user_id !== $userId
            || $run->tool_slug !== self::TOOL_SLUG
            || $run->status !== ToolRunStatus::Succeeded
        ) {
            throw (new ModelNotFoundException)->setModel(
                ToolRun::class,
                [(string) $run->getKey()],
            );
        }

        return $run;
    }
}
