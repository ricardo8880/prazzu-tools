<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Presentation\Controllers;

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
use App\Tools\VacationCalculator\Application\Actions\CalculateTool;
use App\Tools\VacationCalculator\Application\Actions\ManageVacationHistory;
use App\Tools\VacationCalculator\Application\Actions\ShowToolPage;
use App\Tools\VacationCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\VacationCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageVacationHistory $history): View
    {
        return view('tools-pto-calculator::index', [
            ...$page->execute(),
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, UsageMetrics $metrics, Tool $module, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ShowToolPage $page, ManageVacationHistory $history): View
    {
        $startedAt = hrtime(true);
        $input = $request->validated();
        try {
            $result = $action->execute($input)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_salary' => $exception->getMessage()]);
        }

        $saved = false;
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString(now()->format('Y-m-d')), $input, (int) $request->user()->getAuthIdentifier());
            $recorder->succeed($run, $result);
            $saved = true;
        }
        $metrics->record($module->manifest()->slug, 'calculated', $request->user()?->id, (int) ((hrtime(true) - $startedAt) / 1_000_000));

        $request->flash();

        return view('tools-pto-calculator::index', [
            ...$page->execute(),
            'calculationResult' => $result,
            'historySaved' => $saved,
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, string $format, TabularExportService $tabular, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet): JsonResponse|Response|StreamedResponse
    {
        return $this->export($format, $action->execute($request->validated())->toArray(), $request->validated(), $tabular, $documents, $pdf, $spreadsheet);
    }

    public function history(Request $request, ManageVacationHistory $history): View
    {
        return view('tools-pto-calculator::history.index', ['runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1)))]);
    }

    public function repeatHistory(Request $request, string $run, ManageVacationHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.pto-calculator.index')->withInput($entry->input)->with('history_message', 'Saved inputs restored. Review them before recalculating.');
    }

    public function destroyHistory(Request $request, string $run, ManageVacationHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Calculation removed from history.');
    }

    public function exportHistory(Request $request, string $run, string $format, ManageVacationHistory $history, TabularExportService $tabular, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet): JsonResponse|Response|StreamedResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $this->export($format, $entry->result, $entry->input, $tabular, $documents, $pdf, $spreadsheet);
    }

    /** @param array<string,mixed> $result @param array<string,mixed> $input */
    private function export(string $format, array $result, array $input, TabularExportService $tabular, StructuredResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet): JsonResponse|Response|StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'json', 'pdf', 'xlsx'], true), 404);
        $filename = 'pto-calculation-'.now()->format('Y-m-d');
        if ($format === 'json') {
            return response()->json(['input' => $input, 'result' => $result], 200, ['Content-Disposition' => 'attachment; filename="'.$filename.'.json"'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        if ($format === 'csv') {
            $rows = [];
            foreach (($result['summary'] ?? []) as $item) {
                $rows[] = [$item['label'] ?? '', $item['value'] ?? '', $item['description'] ?? ''];
            }

            return $tabular->csv($filename.'.csv', ['Metric', 'Value', 'Description'], $rows);
        }

        return $format === 'pdf'
            ? $pdf->download($documents->pdf('PTO Calculation Report', $filename, $result, $input))
            : $spreadsheet->download($documents->spreadsheet($filename, $result, $input));
    }
}
