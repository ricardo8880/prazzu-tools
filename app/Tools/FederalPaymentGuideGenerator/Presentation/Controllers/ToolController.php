<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\StructuredResultExportFactory;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Http\Controllers\Controller;
use App\Tools\FederalPaymentGuideGenerator\Application\Actions\CalculateGuide;
use App\Tools\FederalPaymentGuideGenerator\Application\Actions\ManageGuideHistory;
use App\Tools\FederalPaymentGuideGenerator\Application\Actions\PrepareGuideExport;
use App\Tools\FederalPaymentGuideGenerator\Application\Actions\ShowToolPage;
use App\Tools\FederalPaymentGuideGenerator\Presentation\Requests\CalculateGuideRequest;
use App\Tools\FederalPaymentGuideGenerator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageGuideHistory $history): View
    {
        return view('tools-gerador-darf-gps::index', [
            ...$page->execute(),
            'result' => $request->session()->get('guide_result'),
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function calculate(CalculateGuideRequest $request, CalculateGuide $action, UsageMetrics $metrics, Tool $tool, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ShowToolPage $page, ManageGuideHistory $history): View
    {
        $startedAt = hrtime(true);
        $input = $request->validated();

        try {
            $result = $action->execute($input);
        } catch (InvalidValue|InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['principal' => $exception->getMessage()]);
        }

        $saved = false;
        if ($persistence->allowsHistory($tool, $request->user())) {
            $run = $recorder->start(
                $tool,
                new RuleVersion((string) $result['normative_rule']['version']),
                ReferenceDate::fromString((string) $input['due_date']),
                $input,
                (int) $request->user()->getAuthIdentifier(),
            );
            $recorder->succeed($run, $result);
            $saved = true;
        }

        $metrics->record($tool->manifest()->slug, 'calculated', $request->user()?->id, null, (int) ((hrtime(true) - $startedAt) / 1_000_000));

        $request->flash();

        return view('tools-gerador-darf-gps::index', [
            ...$page->execute(),
            'result' => $result,
            'historySaved' => $saved,
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function exportCurrent(CalculateGuideRequest $request, CalculateGuide $action, PrepareGuideExport $prepare, string $format, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, TabularExportService $tabular): JsonResponse|StreamedResponse
    {
        return $this->export($format, $request->validated(), $action->execute($request->validated()), $prepare, $documents, $pdf, $spreadsheet, $tabular);
    }

    public function history(Request $request, ManageGuideHistory $history): View
    {
        $favorite = $request->boolean('favorite');
        $page = $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1)), $favorite);

        return view('tools-gerador-darf-gps::history.index', [
            'runs' => new LengthAwarePaginator($page->items, $page->total, $page->perPage, $page->page, ['path' => $request->url(), 'query' => $request->query()]),
            'favorite' => $favorite,
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageGuideHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.gerador-darf-gps.index')->withInput($entry->input)->with('history_message', 'Dados recuperados. Confirme novamente vencimento, Selic e código antes de recalcular.');
    }

    public function toggleFavorite(Request $request, string $run, ManageGuideHistory $history): RedirectResponse
    {
        $favorite = $history->toggleFavorite($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', $favorite ? 'Cálculo adicionado aos favoritos.' : 'Cálculo removido dos favoritos.');
    }

    public function destroyHistory(Request $request, string $run, ManageGuideHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Cálculo removido do histórico.');
    }

    public function exportHistory(Request $request, string $run, string $format, ManageGuideHistory $history, PrepareGuideExport $prepare, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, TabularExportService $tabular): JsonResponse|StreamedResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $this->export($format, $entry->input, $entry->result, $prepare, $documents, $pdf, $spreadsheet, $tabular);
    }

    /** @param array<string, mixed> $input @param array<string, mixed> $result */
    private function export(string $format, array $input, array $result, PrepareGuideExport $prepare, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, TabularExportService $tabular): JsonResponse|StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'json', 'pdf', 'xlsx'], true), 404);
        $export = $prepare->execute($input, $result);

        if ($format === 'json') {
            return response()->json($export['payload'], 200, [
                'Content-Disposition' => 'attachment; filename="'.$export['filename'].'.json"',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        if ($format === 'csv') {
            return $tabular->csv($export['filename'].'.csv', $export['headers'], $export['rows']);
        }

        $filename = $export['filename'];
        return $format === 'pdf'
            ? $pdf->download($documents->pdf('Relatório orientativo de DARF/GPS', $filename, $export['payload'], $input))
            : $spreadsheet->download($documents->spreadsheet($filename, $export['payload'], $input));
    }
}
