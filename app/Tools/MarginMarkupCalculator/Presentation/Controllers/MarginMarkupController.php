<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Http\Controllers\Controller;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkup;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkupBatch;
use App\Tools\MarginMarkupCalculator\Application\Actions\DeleteMarginMarkupHistory;
use App\Tools\MarginMarkupCalculator\Application\Actions\ListMarginMarkupHistory;
use App\Tools\MarginMarkupCalculator\Application\Actions\PrepareMarginMarkupHistoryReport;
use App\Tools\MarginMarkupCalculator\Application\Actions\PreviewProductImport;
use App\Tools\MarginMarkupCalculator\Application\Actions\ProcessProductImport;
use App\Tools\MarginMarkupCalculator\Application\Actions\RepeatMarginMarkupHistory;
use App\Tools\MarginMarkupCalculator\Application\Actions\ShowMarginMarkupHistory;
use App\Tools\MarginMarkupCalculator\Application\Actions\SimulatePricingScenarios;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupBatchRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\PreviewProductImportRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\ProcessProductImportRequest;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\SimulatePricingScenariosRequest;
use App\Tools\MarginMarkupCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class MarginMarkupController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-margem-markup::index');
    }

    public function calculate(
        CalculateMarginMarkupRequest $request,
        CalculateMarginMarkup $action,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $user = $request->user();
        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = null;

        try {
            if ($persistence->allowsHistory($module, $user)) {
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
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = $this->startRun($request, $recorder, $module, $persistence, $input, 'batch');

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
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        UsageMetrics $metrics,
        Tool $module,
    ): RedirectResponse {
        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = $this->startRun($request, $recorder, $module, $persistence, $input, 'scenarios');

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
        UsageMetrics $metrics,
        Tool $module,
        TabularExportService $exporter,
    ): StreamedResponse {
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

        return $exporter->csv('margem-markup.csv', ['Campo', 'Valor'], collect([
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
        ])->map(static fn (string $value, string $label): array => [$label, $value]));
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
        TabularExportService $exporter,
    ): StreamedResponse {
        $input = $request->validated();
        $results = $action->execute($input['products']);

        return $exporter->csv(
            'produtos-margem-markup.csv',
            ['Produto', 'Código', 'Categoria', 'Custo total', 'Preço sugerido', 'Lucro líquido', 'Margem', 'Markup', 'Índice'],
            array_map(static fn (array $row): array => [
                $row['name'], $row['code'], $row['category'], $row['total_cost'], $row['sale_price'],
                $row['net_profit'], $row['margin'], $row['markup'], $row['markup_multiplier'],
            ], $results),
        );
    }

    public function exportScenarios(
        SimulatePricingScenariosRequest $request,
        SimulatePricingScenarios $action,
        TabularExportService $exporter,
    ): StreamedResponse {
        $results = $action->execute($request->validated());

        return $exporter->csv(
            'cenarios-margem-markup.csv',
            ['Cenário', 'Ajuste de custo', 'Margem alvo', 'Desconto', 'Custo total', 'Preço de tabela', 'Preço final', 'Lucro líquido', 'Margem efetiva', 'Índice'],
            array_map(static fn (array $row): array => array_values($row), $results),
        );
    }

    public function history(Request $request, ListMarginMarkupHistory $action): View
    {
        return view('tools-calculadora-margem-markup::history.index', [
            'runs' => $action->execute(
                (int) $request->user()->getAuthIdentifier(),
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
            ),
        ]);
    }

    public function showHistory(
        Request $request,
        ToolRun $run,
        ShowMarginMarkupHistory $action,
    ): View {
        return view(
            'tools-calculadora-margem-markup::history.show',
            $action->execute($run, (int) $request->user()->getAuthIdentifier()),
        );
    }

    public function repeatHistory(
        Request $request,
        ToolRun $run,
        RepeatMarginMarkupHistory $action,
    ): RedirectResponse {
        return redirect()->route('tools.calculadora-margem-markup.index')
            ->withInput($action->execute($run, (int) $request->user()->getAuthIdentifier()))
            ->with('history_message', 'Os dados foram carregados. Revise-os antes de calcular novamente.');
    }

    public function exportHistory(
        Request $request,
        ToolRun $run,
        PrepareMarginMarkupHistoryReport $action,
        BrowserPrintExporter $exporter,
    ): View {
        $report = $action->execute($run, (int) $request->user()->getAuthIdentifier());

        return $this->pdfView(
            $exporter,
            $report['result'],
            $report['input'],
            $report['generatedAt'],
        );
    }

    public function destroyHistory(
        Request $request,
        ToolRun $run,
        DeleteMarginMarkupHistory $action,
    ): RedirectResponse {
        $action->execute($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-margem-markup.history.index')
            ->with('history_message', 'Registro removido do histórico.');
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

    public function importTemplate(TabularExportService $exporter): StreamedResponse
    {
        return $exporter->csv(
            'modelo-importacao-margem-markup.csv',
            ['Produto', 'Código', 'Categoria', 'Custo base', 'Outros custos', 'Frete', 'Embalagem', 'Despesas rateadas', 'Margem %', 'Impostos %', 'Comissão %', 'Cartão %', 'Marketplace %'],
            [['Produto exemplo', 'SKU-001', 'Geral', '100,00', '0,00', '10,00', '2,00', '5,00', '30', '6', '2', '3', '0']],
        );
    }

    private function importOwnerKey(Request $request): string
    {
        return $request->user() !== null
            ? 'user:'.$request->user()->id
            : 'ip:'.($request->ip() ?? 'unknown');
    }

    /** @param array<string, mixed> $input */
    private function startRun(
        Request $request,
        ToolRunRecorder $recorder,
        Tool $module,
        ToolPersistenceAuthorizer $persistence,
        array $input,
        string $type,
    ): ?ToolRun {
        if (! $persistence->allowsHistory($module, $request->user())) {
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

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRun $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
