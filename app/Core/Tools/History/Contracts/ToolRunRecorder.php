<?php

namespace App\Core\Tools\History\Contracts;

use App\Core\Dates\ReferenceDate;
use App\Core\Normative\NormativeReference;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Models\ToolRun;

interface ToolRunRecorder
{
    /** @param array<string, mixed> $input */
    public function start(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        ?int $userId = null,
    ): ToolRun;

    /**
     * @param array<string, mixed> $result
     * @param array<int, NormativeReference> $references
     */
    public function succeed(ToolRun $run, array $result, array $references = []): ToolRun;

    public function fail(ToolRun $run, string $errorCode): ToolRun;
}
