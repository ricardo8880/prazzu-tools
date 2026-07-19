<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Controllers;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Http\Controllers\Controller;
use App\Tools\BusinessDocumentValidator\Application\Actions\AnalyzeCompanyConsistency;
use App\Tools\BusinessDocumentValidator\Application\Actions\BuildBatchExportRows;
use App\Tools\BusinessDocumentValidator\Application\Actions\DeleteValidationHistory;
use App\Tools\BusinessDocumentValidator\Application\Actions\ListValidationHistory;
use App\Tools\BusinessDocumentValidator\Application\Actions\LookupCompanyRegistry;
use App\Tools\BusinessDocumentValidator\Application\Actions\PreviewBatchImport;
use App\Tools\BusinessDocumentValidator\Application\Actions\ProcessBatchValidation;
use App\Tools\BusinessDocumentValidator\Application\Actions\ValidateBusinessDocument;
use App\Tools\BusinessDocumentValidator\Application\Actions\ValidateStateRegistration;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\AnalyzeCompanyConsistencyRequest;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\LookupCompanyRegistryRequest;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\PreviewBatchImportRequest;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ProcessBatchImportRequest;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ValidateBusinessDocumentRequest;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ValidateStateRegistrationRequest;
use App\Tools\BusinessDocumentValidator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class BusinessDocumentValidatorController extends Controller
{
    public function index(Request $request, ListValidationHistory $history): View
    {
        $recentHistory = $request->user() === null
            ? []
            : $history->recent((int) $request->user()->getAuthIdentifier());

        return view('tools-validador-de-cnpj::index', ['recentHistory' => $recentHistory]);
    }

    public function previewBatchImport(PreviewBatchImportRequest $request, PreviewBatchImport $action): RedirectResponse
    {
        try {
            $preview = $action->execute($request->file('batch_file'), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            report($exception);
            return back()->withErrors(['batch_file' => $exception->getMessage()]);
        }

        $request->session()->put('batch_import_preview', $preview);
        $request->session()->forget('batch_validation_result');

        return back();
    }

    public function processBatchImport(
        ProcessBatchImportRequest $request,
        ProcessBatchValidation $action,
        ToolRunRecorder $recorder,
        PlatformAnalytics $analytics,
        Tool $module,
    ): RedirectResponse {
        $run = null;
        $preview = (array) $request->session()->get('batch_import_preview', []);
        $historyInput = [
            'file_name' => $preview['file_name'] ?? 'Importação',
            'format' => $preview['format'] ?? null,
            'total_rows' => $preview['total_rows'] ?? 0,
            'consult_registry' => (bool) $request->boolean('consult_registry'),
        ];

        try {
            if ($request->user() !== null) {
                $run = $recorder->start($module, new RuleVersion('batch-validation-v1'), ReferenceDate::fromDateTime(now()), $historyInput, $request->user()->id);
            }
            $result = $action->execute($request->validated(), $this->importOwnerKey($request));
            $payload = $result->toArray();
            if ($run !== null) {
                $recorder->succeed($run, $payload);
            }
        } catch (RuntimeException $exception) {
            if ($run !== null) {
                $recorder->fail($run, 'batch.processing_failed');
            }
            return back()->withErrors(['batch_import' => $exception->getMessage()]);
        }

        $request->session()->put('batch_validation_result', $payload);
        $analytics->record(AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value, 'tool', $request, $payload['summary'] ?? []);

        return back()->with('history_saved', $run !== null);
    }

    public function exportBatch(Request $request, BuildBatchExportRows $builder, TabularExportService $exporter, PlatformAnalytics $analytics): StreamedResponse|Response
    {
        $result = $this->batchResult($request);
        $format = (string) $request->get('format', 'csv');
        $onlyIssues = $request->boolean('only_issues');
        $rows = $builder->execute($result, $onlyIssues);
        $suffix = $onlyIssues ? '-inconsistencias' : '-completo';

        $analytics->record(AnalyticsEventName::BusinessDocumentValidatorBatchExported->value, 'tool', $request, ['format' => $format, 'only_issues' => $onlyIssues, 'rows' => count($rows)]);

        return $format === 'excel'
            ? $exporter->excel('validacao-documentos'.$suffix.'.xls', $builder->headers(), $rows, 'Validações')
            : $exporter->csv('validacao-documentos'.$suffix.'.csv', $builder->headers(), $rows);
    }

    public function printBatch(Request $request, BrowserPrintExporter $exporter): View
    {
        $result = $this->batchResult($request);

        return $exporter->render(new PrintableDocument(
            title: 'Relatório de validação de documentos',
            subtitle: 'CPF, CNPJ, Inscrição Estadual e inconsistências cadastrais',
            contentView: 'tools-validador-de-cnpj::print.batch-report',
            data: ['result' => $result],
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: 'Registros analisados',
            summaryValue: (string) data_get($result, 'summary.total', 0),
        ));
    }

    public function history(Request $request, ListValidationHistory $history): View
    {
        return view('tools-validador-de-cnpj::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), page: max(1, $request->integer('page', 1))),
        ]);
    }

    public function destroyHistory(
        Request $request,
        string $run,
        DeleteValidationHistory $action,
    ): RedirectResponse {
        $action->execute($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Registro removido do histórico.');
    }

    public function analyzeConsistency(AnalyzeCompanyConsistencyRequest $request, AnalyzeCompanyConsistency $action): RedirectResponse
    {
        return back()->withInput()->with('consistency_analysis_result', $action->execute($request->validated())->toArray());
    }

    public function lookupCompany(LookupCompanyRegistryRequest $request, LookupCompanyRegistry $action): RedirectResponse
    {
        return back()->withInput()->with('registry_lookup_result', $action->execute((string) $request->validated('cnpj'))->toArray());
    }

    public function validateStateRegistration(ValidateStateRegistrationRequest $request, ValidateStateRegistration $action): RedirectResponse
    {
        return back()->withInput()->with('state_registration_result', $action->execute($request->validated())->toArray());
    }

    public function validateDocument(ValidateBusinessDocumentRequest $request, ValidateBusinessDocument $action): RedirectResponse
    {
        return back()->withInput()->with('validation_result', $action->execute($request->validated())->toArray());
    }

    private function batchResult(Request $request): array
    {
        $result = $request->session()->get('batch_validation_result');
        abort_unless(is_array($result), 404, 'Nenhum resultado em lote disponível para exportação.');
        return $result;
    }

    private function importOwnerKey(Request $request): string
    {
        return (string) ($request->user()?->getAuthIdentifier() ?? $request->session()->getId());
    }
}
