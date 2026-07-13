<?php

namespace App\Core\Tools\History\Services;

use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\NormativeReference;
use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\History\Contracts\HasHistoryPolicy;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Exceptions\HistoryDisabled;
use App\Core\Tools\History\Exceptions\InvalidToolRunTransition;
use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Support\Facades\DB;

final readonly class DatabaseToolRunRecorder implements ToolRunRecorder
{
    public function __construct(
        private PayloadProjector $projector,
        private AuditLogger $audit,
    ) {
    }

    public function start(
        ToolModule $module,
        RuleVersion $ruleVersion,
        ReferenceDate $referenceDate,
        array $input,
        ?int $userId = null,
    ): ToolRun {
        if (! $module instanceof HasHistoryPolicy || ! $module->historyPolicy()->enabled) {
            throw new HistoryDisabled('A ferramenta não habilitou persistência de histórico.');
        }

        $manifest = $module->manifest();
        $policy = $module->historyPolicy();

        return DB::transaction(function () use ($userId, $manifest, $ruleVersion, $referenceDate, $input, $policy): ToolRun {
            $run = ToolRun::query()->create([
                'user_id' => $userId,
                'tool_slug' => $manifest->slug,
                'tool_version' => $manifest->version,
                'rule_version' => $ruleVersion->value,
                'reference_date' => $referenceDate->toString(),
                'status' => ToolRunStatus::Running,
                'input_payload' => $this->projector->project($input, $policy->inputFields),
                'started_at' => now(),
                'expires_at' => now()->addDays($policy->retentionDays),
            ]);

            $this->audit->record(
                action: 'tool_run.started',
                auditableType: ToolRun::class,
                auditableId: $run->id,
                metadata: [
                    'tool_slug' => $manifest->slug,
                    'tool_version' => $manifest->version,
                    'rule_version' => $ruleVersion->value,
                ],
                actorId: $userId,
            );

            return $run;
        });
    }

    public function succeed(ToolRun $run, array $result, array $references = []): ToolRun
    {
        $this->assertRunning($run);
        $module = app(\App\Core\Tools\ToolRegistry::class)->findModule($run->tool_slug);

        if (! $module instanceof HasHistoryPolicy) {
            throw new HistoryDisabled('A ferramenta não fornece uma política de histórico.');
        }

        return DB::transaction(function () use ($run, $result, $references, $module): ToolRun {
            $run->forceFill([
                'status' => ToolRunStatus::Succeeded,
                'result_payload' => $this->projector->project($result, $module->historyPolicy()->resultFields),
                'normative_references' => array_map(
                    static fn (NormativeReference $reference): array => $reference->toArray(),
                    $references,
                ),
                'finished_at' => now(),
            ])->save();

            $this->audit->record(
                action: 'tool_run.succeeded',
                auditableType: ToolRun::class,
                auditableId: $run->id,
                metadata: ['tool_slug' => $run->tool_slug],
                actorId: $run->user_id,
            );

            return $run->refresh();
        });
    }

    public function fail(ToolRun $run, string $errorCode): ToolRun
    {
        $this->assertRunning($run);

        if (! preg_match('/^[a-z0-9_.-]{1,100}$/', $errorCode)) {
            throw new \InvalidArgumentException('O código de erro possui formato inválido.');
        }

        return DB::transaction(function () use ($run, $errorCode): ToolRun {
            $run->forceFill([
                'status' => ToolRunStatus::Failed,
                'error_code' => $errorCode,
                'finished_at' => now(),
            ])->save();

            $this->audit->record(
                action: 'tool_run.failed',
                auditableType: ToolRun::class,
                auditableId: $run->id,
                metadata: [
                    'tool_slug' => $run->tool_slug,
                    'error_code' => $errorCode,
                ],
                actorId: $run->user_id,
            );

            return $run->refresh();
        });
    }

    private function assertRunning(ToolRun $run): void
    {
        if ($run->status !== ToolRunStatus::Running) {
            throw new InvalidToolRunTransition('Somente execuções em andamento podem ser finalizadas.');
        }
    }
}
