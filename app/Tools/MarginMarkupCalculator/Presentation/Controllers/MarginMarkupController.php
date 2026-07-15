<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Controllers;

use App\Core\Access\Data\AccessDecision;
use App\Core\Audit\Contracts\AuditLogger;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\ToolExecutionAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Data\UsageLimit;
use App\Http\Controllers\Controller;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkup;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkupBatch;
use App\Tools\MarginMarkupCalculator\Application\Actions\PreviewProductImport;
use App\Tools\MarginMarkupCalculator\Application\Actions\ProcessProductImport;
use App\Tools\MarginMarkupCalculator\Application\Actions\SimulatePricingScenarios;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use App\Tools\MarginMarkupCalculator\Infrastructure\Models\MarginMarkupShare;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupBatchRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CreateMarginMarkupShareRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\PreviewProductImportRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\ProcessProductImportRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\SimulatePricingScenariosRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\UnlockMarginMarkupShareRequest;
use App\Tools\MarginMarkupCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
                $recorder->succeed($run, ['calculation_type' => 'single', ...$result->toArray()]);
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

    public function calculateBatch(
        CalculateMarginMarkupBatchRequest $request,
        CalculateMarginMarkupBatch $action,
        ToolExecutionAuthorizer $authorizer,
        ToolRunRecorder $recorder,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $this->ensureExecutionIsAllowed($request, $authorizer, $module);
        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = $this->startRun($request, $recorder, $module, $input, 'batch');

        try {
            $results = $action->execute($input['products']);
            if ($run !== null) {
                $recorder->succeed($run, ['calculation_type' => 'batch', 'results' => $results]);
            }
        } catch (InvalidValue $exception) {
            $this->recordFailure($recorder, $run, 'batch.invalid_input');
            throw ValidationException::withMessages(['products' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->recordFailure($recorder, $run, 'batch.failed');
            throw $exception;
        }

        $metrics->record(
            toolSlug: $module->manifest()->slug,
            event: 'batch_calculated',
            userId: $request->user()?->id,
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
        );

        return back()->withInput()->with('batch_calculation_results', $results);
    }

    public function simulateScenarios(
        SimulatePricingScenariosRequest $request,
        SimulatePricingScenarios $action,
        ToolExecutionAuthorizer $authorizer,
        ToolRunRecorder $recorder,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $this->ensureExecutionIsAllowed($request, $authorizer, $module);
        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = $this->startRun($request, $recorder, $module, $input, 'scenarios');

        try {
            $results = $action->execute($input);
            if ($run !== null) {
                $recorder->succeed($run, ['calculation_type' => 'scenarios', 'results' => $results]);
            }
        } catch (InvalidValue $exception) {
            $this->recordFailure($recorder, $run, 'scenarios.invalid_input');
            throw ValidationException::withMessages(['scenarios' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->recordFailure($recorder, $run, 'scenarios.failed');
            throw $exception;
        }

        $metrics->record(
            toolSlug: $module->manifest()->slug,
            event: 'scenarios_simulated',
            userId: $request->user()?->id,
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
        );

        return back()->withInput()->with('scenario_simulation_results', $results);
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
                'Lucro bruto' => $result['gross_profit'],
                'Lucro líquido estimado' => $result['net_profit'],
                'Impostos' => $result['taxes_amount'],
                'Comissão' => $result['commission_amount'],
                'Taxas de cartão' => $result['card_fees_amount'],
                'Taxas de marketplace' => $result['marketplace_fees_amount'],
                'Margem' => $result['margin'],
                'Markup' => $result['markup'],
                'Índice de markup' => $result['markup_multiplier'],
                'Versão da regra' => $result['rule_version'],
            ] as $label => $value) {
                fputcsv($handle, [$label, $value], ';');
            }

            fclose($handle);
        }, 'margem-markup.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }


    public function exportPdf(
        CalculateMarginMarkupRequest $request,
        CalculateMarginMarkup $action,
        BrowserPrintExporter $exporter,
    ): View {
        try {
            $input = $request->validated();
            $result = $action->execute($input)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['base_cost' => $exception->getMessage()]);
        }

        return $this->pdfView($exporter, $result, $input, now()->format('d/m/Y H:i'));
    }

    public function exportBatch(
        CalculateMarginMarkupBatchRequest $request,
        CalculateMarginMarkupBatch $action,
    ): StreamedResponse {
        $input = $request->validated();
        $results = $action->execute($input['products']);

        return $this->tabularDownload('produtos-margem-markup.csv', [
            ['Produto', 'Código', 'Categoria', 'Custo total', 'Preço sugerido', 'Lucro líquido', 'Margem', 'Markup', 'Índice'],
            ...array_map(static fn (array $row): array => [
                $row['name'], $row['code'], $row['category'], $row['total_cost'], $row['sale_price'],
                $row['net_profit'], $row['margin'], $row['markup'], $row['markup_multiplier'],
            ], $results),
        ]);
    }

    public function exportScenarios(
        SimulatePricingScenariosRequest $request,
        SimulatePricingScenarios $action,
    ): StreamedResponse {
        $results = $action->execute($request->validated());

        return $this->tabularDownload('cenarios-margem-markup.csv', [
            ['Cenário', 'Ajuste de custo', 'Margem alvo', 'Desconto', 'Custo total', 'Preço de tabela', 'Preço final', 'Lucro líquido', 'Margem efetiva', 'Índice'],
            ...array_map(static fn (array $row): array => array_values($row), $results),
        ]);
    }

    public function history(Request $request): View
    {
        $query = ToolRun::query()
            ->where('user_id', $request->user()->id)
            ->where('tool_slug', 'calculadora-margem-markup')
            ->where('status', ToolRunStatus::Succeeded)
            ->latest('finished_at');

        if ($request->filled('from')) {
            $query->whereDate('reference_date', '>=', $request->string('from')->toString());
        }
        if ($request->filled('to')) {
            $query->whereDate('reference_date', '<=', $request->string('to')->toString());
        }

        return view('tools-calculadora-margem-markup::history.index', [
            'runs' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function showHistory(Request $request, ToolRun $run): View
    {
        $run = $this->ownedRun($request, $run);
        $activeShare = MarginMarkupShare::query()
            ->where('tool_run_id', $run->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('revoked_at')
            ->where(static function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        return view('tools-calculadora-margem-markup::history.show', compact('run', 'activeShare'));
    }

    public function repeatHistory(Request $request, ToolRun $run): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);

        return redirect()->route('tools.calculadora-margem-markup.index')
            ->withInput($run->input_payload ?? [])
            ->with('history_message', 'Os dados foram carregados. Revise-os antes de calcular novamente.');
    }

    public function exportHistory(Request $request, ToolRun $run, BrowserPrintExporter $exporter): View
    {
        $run = $this->ownedRun($request, $run);
        $result = $run->result_payload ?? [];
        abort_unless(($result['calculation_type'] ?? 'single') === 'single', 422, 'O PDF está disponível para cálculos individuais.');

        return $this->pdfView($exporter, $result, $run->input_payload ?? [], $run->finished_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'));
    }

    public function destroyHistory(Request $request, ToolRun $run, AuditLogger $audit): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);
        $audit->record('tool_run.deleted', ToolRun::class, $run->id, ['tool_slug' => $run->tool_slug], $request->user()->id);
        $run->delete();

        return redirect()->route('tools.calculadora-margem-markup.history.index')
            ->with('history_message', 'Registro removido do histórico.');
    }


    public function createShare(CreateMarginMarkupShareRequest $request, ToolRun $run): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);
        abort_unless(($run->result_payload['calculation_type'] ?? 'single') === 'single', 422, 'O compartilhamento está disponível para cálculos individuais.');

        $share = MarginMarkupShare::query()->updateOrCreate(
            ['tool_run_id' => $run->id, 'user_id' => $request->user()->id, 'revoked_at' => null],
            [
                'token' => (string) Str::uuid(),
                'access_code_hash' => $request->filled('access_code') ? Hash::make((string) $request->input('access_code')) : null,
                'expires_at' => now()->addDays((int) $request->integer('validity_days')),
            ],
        );

        return back()->with('share_url', route('tools.calculadora-margem-markup.shared.show', $share->token));
    }

    public function revokeShare(Request $request, ToolRun $run): RedirectResponse
    {
        $run = $this->ownedRun($request, $run);
        MarginMarkupShare::query()
            ->where('tool_run_id', $run->id)
            ->where('user_id', $request->user()->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        return back()->with('history_message', 'Link de compartilhamento revogado.');
    }

    public function shared(string $token, Request $request): View
    {
        $share = $this->availableShare($token);
        $unlocked = ! $share->isProtected() || $request->session()->get($this->shareSessionKey($share)) === true;
        $run = $unlocked ? ToolRun::query()->findOrFail($share->tool_run_id) : null;

        return view('tools-calculadora-margem-markup::shared.show', compact('share', 'run', 'unlocked'));
    }

    public function unlockShared(string $token, UnlockMarginMarkupShareRequest $request): RedirectResponse
    {
        $share = $this->availableShare($token);

        if (! $share->isProtected() || Hash::check((string) $request->input('access_code'), (string) $share->access_code_hash)) {
            $request->session()->put($this->shareSessionKey($share), true);
            return redirect()->route('tools.calculadora-margem-markup.shared.show', $share->token);
        }

        throw ValidationException::withMessages(['access_code' => 'Código de acesso inválido.']);
    }

    public function previewImport(PreviewProductImportRequest $request, PreviewProductImport $action): RedirectResponse
    {
        try {
            $preview = $action->execute($request->file('import_file'), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['import_file' => $exception->getMessage()]);
        }

        return back()->with('product_import_preview', $preview);
    }

    public function processImport(ProcessProductImportRequest $request, ProcessProductImport $action): RedirectResponse
    {
        try {
            $result = $action->execute($request->validated(), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['import_token' => $exception->getMessage()]);
        }

        return back()
            ->withInput(['products' => $result['products']])
            ->with('product_import_result', $result);
    }

    public function importTemplate(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) { return; }
            fputcsv($handle, ['Produto','Código','Categoria','Custo base','Outros custos','Frete','Embalagem','Despesas rateadas','Margem %','Impostos %','Comissão %','Cartão %','Marketplace %'], ';');
            fputcsv($handle, ['Produto exemplo','SKU-001','Geral','100,00','0,00','10,00','2,00','5,00','30','6','2','3','0'], ';');
            fclose($handle);
        }, 'modelo-importacao-margem-markup.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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

    private function importOwnerKey(Request $request): string
    {
        return $request->user() !== null
            ? 'user:'.$request->user()->id
            : 'ip:'.($request->ip() ?? 'unknown');
    }

    /** @param array<string, mixed> $input */
    private function startRun(Request $request, ToolRunRecorder $recorder, Tool $module, array $input, string $type): ?ToolRun
    {
        if ($request->user() === null) {
            return null;
        }
        $input['calculation_type'] = $type;

        return $recorder->start(
            module: $module,
            ruleVersion: new RuleVersion(MarginMarkupCalculator::RULE_VERSION),
            referenceDate: ReferenceDate::fromString((string) $input['reference_date']),
            input: $input,
            userId: $request->user()->id,
        );
    }

    private function ownedRun(Request $request, ToolRun $run): ToolRun
    {
        abort_unless(
            $run->user_id === $request->user()->id
            && $run->tool_slug === 'calculadora-margem-markup'
            && $run->status === ToolRunStatus::Succeeded,
            404,
        );

        return $run;
    }

    /** @param array<int, array<int, string>> $rows */
    private function tabularDownload(string $filename, array $rows): StreamedResponse
    {
        return response()->streamDownload(static function () use ($rows): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) { return; }
            fwrite($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) { fputcsv($handle, $row, ';'); }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @param array<string, mixed> $result @param array<string, mixed> $input */
    private function pdfView(BrowserPrintExporter $exporter, array $result, array $input, string $generatedAt): View
    {
        return $exporter->render(new PrintableDocument(
            title: 'Relatório de Margem, Markup e Formação de Preço',
            subtitle: 'Composição do preço de venda e rentabilidade estimada',
            contentView: 'tools-calculadora-margem-markup::pdf.report',
            data: ['result' => $result, 'input' => $input],
            generatedAt: $generatedAt,
            summaryLabel: 'Preço de venda sugerido',
            summaryValue: $result['sale_price'] ?? '—',
        ));
    }


    private function availableShare(string $token): MarginMarkupShare
    {
        $share = MarginMarkupShare::query()->where('token', $token)->firstOrFail();
        abort_unless($share->isAvailable(), 410, 'Este link expirou ou foi revogado.');

        return $share;
    }

    private function shareSessionKey(MarginMarkupShare $share): string
    {
        return 'margin_markup_share_unlocked_'.$share->token;
    }

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRun $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
