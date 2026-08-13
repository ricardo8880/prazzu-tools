<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\Export\Services\TabularExportService;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class MarginMarkupController extends Controller
{
    public function __construct(private readonly ToolResultResolver $resolver) {}

    public function index(): View
    {
        return view('tools-calculadora-margem-markup::index', [
            'taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1),
        ]);
    }

    public function calculate(
        CalculateMarginMarkupRequest $request,
        CalculateMarginMarkup $action,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        UsageMetrics $metrics,
        Tool $module,
        ToolResultPublisher $integrations,
    ): View {
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

            $resultData = $result->toArray();
            $integrations->publish(new IntegrationPayload(
                sourceTool: 'calculadora-margem-markup',
                contractName: 'pricing-scenario',
                contractVersion: 1,
                data: [
                    'product_name' => (string) ($input['product_name'] ?? ''),
                    'sale_price' => (string) $resultData['sale_price'],
                    'margin' => (string) $resultData['margin'],
                    'markup' => (string) $resultData['markup'],
                ],
            ));

            $metrics->record(
                toolSlug: $module->manifest()->slug,
                event: 'calculated',
                userId: $user?->id,
                durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
            );

            $request->flash();

            return view('tools-calculadora-margem-markup::index', [
                'taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1),
                'calculationResult' => $resultData,
            ]);
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
    ): View {
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

        $request->flash();

        return view('tools-calculadora-margem-markup::index', [
            'taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1),
            'batchCalculationResults' => $results,
        ]);
    }

    public function simulateScenarios(
        SimulatePricingScenariosRequest $request,
        SimulatePricingScenarios $action,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        UsageMetrics $metrics,
        Tool $module,
    ): View {
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

        $request->flash();

        return view('tools-calculadora-margem-markup::index', [
            'taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1),
            'scenarioSimulationResults' => $results,
        ]);
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
        PdfExporter $exporter,
        StructuredResultExportFactory $documents,
    ): Response {
        $input = $request->validated();

        Log::info('Margin/markup PDF export requested.', [
            'tool' => Tool::SLUG,
            'reference_date' => $input['reference_date'] ?? null,
            'user_id' => $request->user()?->getAuthIdentifier(),
        ]);

        try {
            $result = $action->execute($input)->toArray();
            $document = $documents->pdf(
                'Calculadora de Precificação de Produtos',
                'margem-markup-'.now()->format('Y-m-d'),
                $result,
                $input,
            );

            return $exporter->download($document);
        } catch (InvalidValue $exception) {
            Log::warning('Margin/markup PDF export validation failed.', [
                'tool' => Tool::SLUG,
                'message' => $exception->getMessage(),
            ]);
            throw ValidationException::withMessages(['base_cost' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            Log::error('Margin/markup PDF export failed.', [
                'tool' => Tool::SLUG,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
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
                max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function showHistory(
        Request $request,
        string $run,
        ShowMarginMarkupHistory $action,
    ): View {
        return view(
            'tools-calculadora-margem-markup::history.show',
            $action->execute($run, (int) $request->user()->getAuthIdentifier()),
        );
    }

    public function repeatHistory(
        Request $request,
        string $run,
        RepeatMarginMarkupHistory $action,
    ): RedirectResponse {
        return redirect()->route('tools.calculadora-margem-markup.index')
            ->withInput($action->execute($run, (int) $request->user()->getAuthIdentifier()))
            ->with('history_message', 'Os dados foram carregados. Revise-os antes de calcular novamente.');
    }

    public function exportHistory(
        Request $request,
        string $run,
        PrepareMarginMarkupHistoryReport $action,
        StructuredResultExportFactory $documents,
    ): View {
        Log::info('Margin/markup history PDF view requested.', [
            'tool' => Tool::SLUG,
            'run_id' => $run,
            'user_id' => $request->user()?->getAuthIdentifier(),
        ]);

        try {
            $report = $action->execute($run, (int) $request->user()->getAuthIdentifier());
            $document = $documents->pdf(
                'Relatório de Margem, Markup e Formação de Preço',
                'margem-markup-historico-'.$run,
                $report['result'],
                $report['input'],
            );

            return view($document->view, $document->data);
        } catch (Throwable $exception) {
            Log::error('Margin/markup history PDF view failed.', [
                'tool' => Tool::SLUG,
                'run_id' => $run,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function destroyHistory(
        Request $request,
        string $run,
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

        $request->session()->flash('product_import_preview', $preview);

        return redirect()->route('tools.calculadora-margem-markup.index');
    }

    public function processImport(ProcessProductImportRequest $request, ProcessProductImport $action): RedirectResponse
    {
        try {
            $result = $action->execute($request->validated(), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['import_token' => $exception->getMessage()]);
        }

        $request->session()->flashInput(['products' => $result['products']]);
        $request->session()->flash('product_import_result', $result);

        return redirect()->route('tools.calculadora-margem-markup.index');
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
    ): ?ToolRunHandle {
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

    private function recordFailure(ToolRunRecorder $recorder, ?ToolRunHandle $run, string $errorCode): void
    {
        if ($run !== null) {
            $recorder->fail($run, $errorCode);
        }
    }
}
