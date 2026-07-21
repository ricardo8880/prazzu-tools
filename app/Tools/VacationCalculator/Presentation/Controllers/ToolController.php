<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Http\Controllers\Controller;
use App\Tools\VacationCalculator\Application\Actions\CalculateTool;
use App\Tools\VacationCalculator\Application\Actions\ManageVacationHistory;
use App\Tools\VacationCalculator\Application\Actions\PlanVacations;
use App\Tools\VacationCalculator\Application\Actions\ShowToolPage;
use App\Tools\VacationCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\VacationCalculator\Presentation\Requests\PlanVacationsRequest;
use App\Tools\VacationCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageVacationHistory $history): View
    {
        return view('tools-calculadora-ferias::index', [
            ...$page->execute(),
            'recentHistory' => $request->user() ? $history->recent((int) $request->user()->getAuthIdentifier()) : [],
        ]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, UsageMetrics $metrics, Tool $module, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence): RedirectResponse
    {
        $startedAt = hrtime(true);
        $input = $request->validated();
        try { $result = $action->execute($input)->toArray(); }
        catch (InvalidValue $exception) { throw ValidationException::withMessages(['monthly_salary' => $exception->getMessage()]); }

        $saved = false;
        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start($module, new RuleVersion('2026.1'), ReferenceDate::fromString((string) $input['vacation_start_date']), $input, (int) $request->user()->getAuthIdentifier());
            $recorder->succeed($run, $result); $saved = true;
        }
        $metrics->record($module->manifest()->slug, 'calculated', $request->user()?->id, (int) ((hrtime(true) - $startedAt) / 1_000_000));

        return redirect()->route('tools.calculadora-ferias.index')
            ->withInput()
            ->with('calculation_result', $result)
            ->with('history_saved', $saved);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, string $format, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    { return $this->export($format, $action->execute($request->validated())->toArray(), $request->validated(), $print, $tabular); }

    public function history(Request $request, ManageVacationHistory $history): View
    { return view('tools-calculadora-ferias::history.index', ['runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1)))]); }

    public function repeatHistory(Request $request, string $run, ManageVacationHistory $history): RedirectResponse
    { $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier()); return redirect()->route('tools.calculadora-ferias.index')->withInput($entry->input)->with('history_message', 'Dados recuperados. Revise as datas antes de recalcular.'); }

    public function destroyHistory(Request $request, string $run, ManageVacationHistory $history): RedirectResponse
    { $history->delete($run, (int) $request->user()->getAuthIdentifier()); return back()->with('history_message', 'Cálculo removido do histórico.'); }

    public function exportHistory(Request $request, string $run, string $format, ManageVacationHistory $history, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    { $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier()); return $this->export($format, $entry->result, $entry->input, $print, $tabular); }

    public function planner(): View { return view('tools-calculadora-ferias::planner'); }
    public function plan(PlanVacationsRequest $request, PlanVacations $action): View
    { return view('tools-calculadora-ferias::planner', ['plan' => $action->execute($request->validated('employees'))]); }

    /** @param array<string,mixed> $result @param array<string,mixed> $input */
    private function export(string $format, array $result, array $input, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    {
        abort_unless(in_array($format, ['csv','json','pdf'], true), 404);
        $filename = 'ferias-'.($input['vacation_start_date'] ?? now()->format('Y-m-d'));
        if ($format === 'json') return response()->json(['input'=>$input,'result'=>$result], 200, ['Content-Disposition'=>'attachment; filename="'.$filename.'.json"'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        if ($format === 'csv') {
            $rows = [];
            foreach (($result['summary'] ?? []) as $item) $rows[] = [$item['label'] ?? '', $item['value'] ?? '', $item['description'] ?? ''];
            return $tabular->csv($filename.'.csv', ['Indicador','Valor','Descrição'], $rows);
        }
        return $print->render(new PrintableDocument('Relatório de Férias', 'Início em '.($input['vacation_start_date'] ?? '—'), 'tools-calculadora-ferias::pdf.report', ['input'=>$input,'result'=>$result], now()->format('d/m/Y H:i'), 'Total estimado', $result['summary'][4]['value'] ?? '—'));
    }
}
