<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Controllers;

use App\Core\Access\Data\AccessDecision;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\ToolExecutionAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Data\UsageLimit;
use App\Http\Controllers\Controller;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkup;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupRequest;
use App\Tools\MarginMarkupCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class MarginMarkupController extends Controller
{
    private const USAGE_LIMIT = 20;

    private const USAGE_WINDOW_SECONDS = 3600;

    public function index(): View
    {
        return view('tools-calculadora-margem-markup::index');
    }

    public function calculate(
        CalculateMarginMarkupRequest $request,
        CalculateMarginMarkup $action,
        ToolExecutionAuthorizer $authorizer,
        ToolRunRecorder $recorder,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $user = $request->user();
        $this->ensureExecutionIsAllowed($request, $authorizer, $module);

        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = null;

        try {
            // O histórico persistente fica restrito a usuários autenticados.
            if ($user !== null) {
                $run = $recorder->start(
                    module: $module,
                    ruleVersion: new RuleVersion(MarginMarkupCalculator::RULE_VERSION),
                    referenceDate: ReferenceDate::fromString($input['reference_date']),
                    input: $input,
                    userId: $user->id,
                );
            }

            $result = $action->execute($input);

            if ($run !== null) {
                $recorder->succeed($run, $result->toArray());
            }

            $metrics->record(
                toolSlug: $module->manifest()->slug,
                event: 'calculated',
                userId: $user?->id,
                durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
            );

            return back()
                ->withInput()
                ->with('calculation_result', $result->toArray());
        } catch (InvalidValue $exception) {
            $this->recordFailure($recorder, $run, 'calculation.invalid_input');

            throw ValidationException::withMessages([
                'base_cost' => $exception->getMessage(),
            ]);
        } catch (Throwable $exception) {
            $this->recordFailure($recorder, $run, 'calculation.failed');

            throw $exception;
        }
    }

    public function export(
        CalculateMarginMarkupRequest $request,
        CalculateMarginMarkup $action,
        ToolExecutionAuthorizer $authorizer,
        UsageMetrics $metrics,
        Tool $module,
    ): StreamedResponse {
        $this->ensureExecutionIsAllowed($request, $authorizer, $module);

        $validated = $request->validated();
        $startedAt = hrtime(true);

        try {
            $result = $action->execute($validated)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages([
                'base_cost' => $exception->getMessage(),
            ]);
        }

        $metrics->record(
            toolSlug: $module->manifest()->slug,
            event: 'exported',
            userId: $request->user()?->id,
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
        );

        return response()->streamDownload(function () use ($validated, $result): void {
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Campo', 'Valor'], ';');

            foreach ([
                'Data de referência' => $validated['reference_date'],
                'Custo total' => $result['total_cost'],
                'Preço de venda' => $result['sale_price'],
                'Lucro' => $result['profit'],
                'Margem' => $result['margin'],
                'Markup' => $result['markup'],
                'Versão da regra' => $result['rule_version'],
            ] as $label => $value) {
                fputcsv($handle, [$label, $value], ';');
            }

            fclose($handle);
        }, 'margem-markup.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureExecutionIsAllowed(
        Request $request,
        ToolExecutionAuthorizer $authorizer,
        Tool $module,
    ): void {
        $user = $request->user();
        $context = new ToolAccessContext(
            userId: $user?->id,
            role: $user?->role ?? AccountRole::User,
            plan: $user?->subscription_plan ?? SubscriptionPlan::Free,
        );
        $subject = $user !== null ? 'user:'.$user->id : 'ip:'.($request->ip() ?? 'unknown');
        $decision = $authorizer->authorize(
            manifest: $module->manifest(),
            context: $context,
            subjectKey: $subject,
            limit: new UsageLimit(self::USAGE_LIMIT, self::USAGE_WINDOW_SECONDS),
        );

        if (! $decision->allowed) {
            $this->abortForDeniedAccess($decision);
        }
    }

    private function abortForDeniedAccess(AccessDecision $decision): never
    {
        match ($decision->reason) {
            'tool.authentication_required' => abort(401, 'É necessário entrar para utilizar esta ferramenta.'),
            'tool.premium_required', 'tool.internal_only' => abort(403, 'Você não possui permissão para utilizar esta ferramenta.'),
            'tool.feature_disabled', 'tool.status_blocks_execution' => abort(503, 'Esta ferramenta está temporariamente indisponível.'),
            'tool.usage_limit_reached' => abort(429, 'O limite de uso desta ferramenta foi atingido.'),
            default => abort(403, 'A execução desta ferramenta não foi autorizada.'),
        };
    }

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRun $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
