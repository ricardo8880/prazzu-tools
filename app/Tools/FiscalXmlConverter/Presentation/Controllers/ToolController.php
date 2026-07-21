<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Http\Controllers\Controller;
use App\Tools\FiscalXmlConverter\Application\Actions\ConvertUploadedXml;
use App\Tools\FiscalXmlConverter\Application\Actions\ConvertUploadedXmlBatch;
use App\Tools\FiscalXmlConverter\Application\Actions\ManageFiscalXmlHistory;
use App\Tools\FiscalXmlConverter\Application\Actions\ShowToolPage;
use App\Tools\FiscalXmlConverter\Domain\Exceptions\InvalidFiscalXml;
use App\Tools\FiscalXmlConverter\Presentation\Requests\ConvertFiscalXmlBatchRequest;
use App\Tools\FiscalXmlConverter\Presentation\Requests\ConvertFiscalXmlRequest;
use App\Tools\FiscalXmlConverter\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageFiscalXmlHistory $history): View
    {
        return view('tools-conversor-fiscal-xml::index', [
            ...$page->execute(),
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function convert(ConvertFiscalXmlRequest $request, ConvertUploadedXml $action, PlatformAnalytics $analytics, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module): RedirectResponse
    {
        try {
            $document = $action->execute($request->file('xml_file'));
        } catch (InvalidFiscalXml $exception) {
            return back()->withErrors(['xml_file' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            report($exception);
            return back()->withErrors(['xml_file' => 'Não foi possível processar o XML. Verifique o arquivo e tente novamente.']);
        }

        $payload = $document->toArray();
        $request->session()->put('fiscal_xml_result', $payload);
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start($module, new RuleVersion('2026.1'), ReferenceDate::fromDateTime(now()), [
                'mode' => 'single', 'model' => $payload['model'], 'access_key' => $payload['access_key'],
                'number' => $payload['number'], 'series' => $payload['series'],
            ], (int) $request->user()->getAuthIdentifier());
            $recorder->succeed($run, $payload);
        }
        $analytics->record(AnalyticsEventName::ToolCalculationCompleted->value, 'tool', $request, [
            'tool' => Tool::SLUG, 'mode' => 'single', 'model' => $payload['model'], 'items' => count($payload['items']),
        ]);

        return back()->with('conversion_success', true);
    }

    public function batch(ConvertFiscalXmlBatchRequest $request, ConvertUploadedXmlBatch $action, PlatformAnalytics $analytics, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module): RedirectResponse
    {
        $result = $action->execute($request->file('xml_files', []));
        if ($result['summary']['processed'] === 0) {
            return back()->withErrors(['xml_files' => 'Nenhum dos XMLs enviados pôde ser processado.'])->with('batch_errors', $result['errors']);
        }
        $request->session()->put('fiscal_xml_batch_result', $result);
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start($module, new RuleVersion('2026.1'), ReferenceDate::fromDateTime(now()), [
                'mode' => 'batch', 'received' => $result['summary']['received'],
            ], (int) $request->user()->getAuthIdentifier());
            $recorder->succeed($run, $result);
        }
        $analytics->record(AnalyticsEventName::ToolCalculationCompleted->value, 'tool', $request, [
            'tool' => Tool::SLUG, 'mode' => 'batch', 'documents' => $result['summary']['processed'], 'items' => $result['summary']['items'],
        ]);
        return back()->with('batch_success', true);
    }

    public function exportCurrent(Request $request, string $format, TabularExportService $tabular): JsonResponse|Response|StreamedResponse
    {
        $result = $request->session()->get('fiscal_xml_batch_result') ?? $request->session()->get('fiscal_xml_result');
        abort_unless(is_array($result), 404);
        return $this->export($format, $result, $tabular);
    }

    public function history(Request $request, ManageFiscalXmlHistory $history): View
    {
        return view('tools-conversor-fiscal-xml::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageFiscalXmlHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());
        $key = ($entry->input['mode'] ?? 'single') === 'batch' ? 'fiscal_xml_batch_result' : 'fiscal_xml_result';
        return redirect()->route('tools.conversor-fiscal-xml.index')->with($key, $entry->result)->with('history_message', 'Resultado recuperado do histórico.');
    }

    public function destroyHistory(Request $request, string $run, ManageFiscalXmlHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());
        return back()->with('history_message', 'Processamento removido do histórico.');
    }

    public function exportHistory(Request $request, string $run, string $format, ManageFiscalXmlHistory $history, TabularExportService $tabular): JsonResponse|Response|StreamedResponse
    {
        return $this->export($format, $history->owned($run, (int) $request->user()->getAuthIdentifier())->result, $tabular);
    }

    private function export(string $format, array $result, TabularExportService $tabular): JsonResponse|Response|StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'json', 'xlsx'], true), 404);
        $documents = isset($result['documents']) ? $result['documents'] : [$result];
        if ($format === 'json') {
            return response()->json($result, 200, ['Content-Disposition' => 'attachment; filename="xml-fiscal.json"'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        $rows = [];
        foreach ($documents as $document) {
            foreach ($document['items'] ?? [] as $item) {
                $rows[] = [
                    $document['source_file'] ?? '', $document['model'] ?? '', $document['access_key'] ?? '',
                    $document['number'] ?? '', $document['series'] ?? '', data_get($document, 'issuer.tax_id', ''),
                    $item['number'] ?? '', $item['code'] ?? '', $item['description'] ?? '', $item['ncm'] ?? '',
                    $item['cfop'] ?? '', $item['quantity'] ?? '', $item['unit'] ?? '', $item['unit_value'] ?? '',
                    $item['total_value'] ?? '', data_get($item, 'taxes.icms', ''), data_get($item, 'taxes.ipi', ''),
                    data_get($item, 'taxes.pis', ''), data_get($item, 'taxes.cofins', ''),
                ];
            }
        }
        $headers = ['Arquivo','Modelo','Chave','Número','Série','CNPJ/CPF emitente','Item','Código','Descrição','NCM','CFOP','Quantidade','Unidade','Valor unitário','Valor total','ICMS','IPI','PIS','Cofins'];
        return $format === 'csv'
            ? $tabular->csv('xml-fiscal.csv', $headers, $rows)
            : $tabular->excel('xml-fiscal.xls', $headers, $rows, 'Itens fiscais');
    }
}
