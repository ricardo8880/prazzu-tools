<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Contracts;

use App\Core\Dates\ReferenceDate;
use App\Core\Normative\NormativeReference;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;

interface ToolRunRecorder
{
    /** @param array<string, mixed> $input */
    public function start(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        ?int $userId = null,
    ): ToolRunHandle;

    /**
     * @param array<string, mixed> $result
     * @param array<int, NormativeReference> $references
     */
    public function succeed(ToolRunHandle $run, array $result, array $references = []): ToolRunHandle;

    public function fail(ToolRunHandle $run, string $errorCode): ToolRunHandle;
}
