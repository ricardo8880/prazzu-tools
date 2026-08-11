<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Controllers;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Temporary\Contracts\TemporaryPayloadStore;
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
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class BusinessDocumentValidatorController extends Controller
{
    private const TOOL_SLUG = 'validador-de-cnpj';
    public function index(Request $request, ListValidationHistory $history): View
    {
        $recentHistory = $request->user() === null
            ? []
            : $history->recent((int) $request->user()->getAuthIdentifier());

        return view('tools-validador-de-cnpj::index', ['recentHistory' => $recentHistory]);
    }

    public function previewBatchImport(PreviewBatchImportRequest $request, PreviewBatchImport $action): RedirectResponse|View
    {
        try {
            $preview = $action->execute($request->file('batch_file'), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['batch_file' => $exception->getMessage()]);
        }

        return view('tools-validador-de-cnpj::index', [
            'recentHistory' => [],
            'batchImportPreview' => $preview,
        ]);
    }

    public function processBatchImport(
        ProcessBatchImportRequest $request,
        ProcessBatchValidation $action,
        ToolRunRecorder $recorder,
        PlatformAnalytics $analytics,
        Tool $module,
        TemporaryPayloadStore $temporary,
    ): RedirectResponse|View {
        $run = null;
        $historyInput = [
            'file_name' => 'Importação em lote',
            'format' => null,
            'total_rows' => 0,
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

        $resultToken = $temporary->put('business-document-validator.batch', $payload, $this->temporaryOwnerKey($request));
        $analytics->track(new AnalyticsEvent(
            name: AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value,
            channel: 'tool',
            properties: $payload['summary'] ?? [],
            subjectType: 'tool',
            subjectSlug: self::TOOL_SLUG,
        ), $request);

        return view('tools-validador-de-cnpj::index', [
            'recentHistory' => [],
            'batchValidationResult' => $payload,
            'batchResultToken' => $resultToken,
            'historySaved' => $run !== null,
        ]);
    }

    public function exportBatch(Request $request, BuildBatchExportRows $builder, TabularExportService $exporter, SpreadsheetExporter $spreadsheet, PlatformAnalytics $analytics, TemporaryPayloadStore $temporary): StreamedResponse|Response
    {
        $result = $this->batchResult($request, $temporary);
        $format = (string) $request->get('format', 'csv');
        $onlyIssues = $request->boolean('only_issues');
        $rows = $builder->execute($result, $onlyIssues);
        $suffix = $onlyIssues ? '-inconsistencias' : '-completo';

        $analytics->track(new AnalyticsEvent(
            name: AnalyticsEventName::BusinessDocumentValidatorBatchExported->value,
            channel: 'tool',
            properties: ['format' => $format, 'only_issues' => $onlyIssues, 'rows' => count($rows)],
            subjectType: 'tool',
            subjectSlug: self::TOOL_SLUG,
        ), $request);

        return $format === 'excel'
            ? $spreadsheet->download(new \App\Core\Export\Data\SpreadsheetDocument('validacao-documentos'.$suffix, [new \App\Core\Export\Data\SpreadsheetSheet('Validações', [$builder->headers(), ...$rows])]))
            : $exporter->csv('validacao-documentos'.$suffix.'.csv', $builder->headers(), $rows);
    }

    public function exportBatchOfficial(Request $request, string $format, TemporaryPayloadStore $temporary, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $result = $this->batchResult($request, $temporary);
        $filename = 'validacao-documentos-lote-'.now()->format('Y-m-d');
        return $format === 'pdf'
            ? $pdf->download($documents->pdf('Validação de documentos em lote', $filename, $result))
            : $spreadsheet->download($documents->spreadsheet($filename, $result));
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

    public function analyzeConsistency(AnalyzeCompanyConsistencyRequest $request, AnalyzeCompanyConsistency $action): View
    {
        $request->flash();
        return view('tools-validador-de-cnpj::index', ['recentHistory' => [], 'consistencyAnalysisResult' => $action->execute($request->validated())->toArray()]);
    }

    public function lookupCompany(LookupCompanyRegistryRequest $request, LookupCompanyRegistry $action): View
    {
        $request->flash();
        return view('tools-validador-de-cnpj::index', ['recentHistory' => [], 'registryLookupResult' => $action->execute((string) $request->validated('cnpj'))->toArray()]);
    }

    public function validateStateRegistration(ValidateStateRegistrationRequest $request, ValidateStateRegistration $action): View
    {
        $request->flash();
        return view('tools-validador-de-cnpj::index', ['recentHistory' => [], 'stateRegistrationResult' => $action->execute($request->validated())->toArray()]);
    }

    public function validateDocument(ValidateBusinessDocumentRequest $request, ValidateBusinessDocument $action): View
    {
        $request->flash();
        return view('tools-validador-de-cnpj::index', ['recentHistory' => [], 'validationResult' => $action->execute($request->validated())->toArray()]);
    }

    private function batchResult(Request $request, TemporaryPayloadStore $temporary): array
    {
        $token = (string) $request->query('result_token', '');
        $result = $temporary->get('business-document-validator.batch', $token, $this->temporaryOwnerKey($request));
        abort_unless(is_array($result), 404, 'Nenhum resultado em lote disponível para exportação.');

        return $result;
    }

    private function importOwnerKey(Request $request): string
    {
        return $this->temporaryOwnerKey($request);
    }

    private function temporaryOwnerKey(Request $request): string
    {
        if ($request->user() !== null) {
            return 'user:'.$request->user()->getAuthIdentifier();
        }

        return 'guest:'.hash('sha256', ($request->ip() ?? 'unknown').'|'.($request->userAgent() ?? 'unknown'));
    }
}
