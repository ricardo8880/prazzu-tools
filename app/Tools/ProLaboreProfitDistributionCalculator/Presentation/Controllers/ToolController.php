<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Http\Controllers\Controller;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\CalculateTool;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\ManageToolHistory;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\ShowToolPage;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\SimulateScenarios;
use App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Requests\SimulateScenariosRequest;
use App\Tools\ProLaboreProfitDistributionCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ManageToolHistory $history): View
    {
        return view('tools-calculadora-pro-labore-distribuicao-lucros::index', [
            ...$page->execute(),
            'recentHistory' => $request->user() === null ? [] : $history->recent((int) $request->user()->getAuthIdentifier()),
        ]);
    }

    public function calculate(
        ExecuteToolRequest $request,
        CalculateTool $action,
        ShowToolPage $page,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
    ): View {
        $input = $request->validated();
        $result = $action->execute($input);
        $saved = false;

        if ($persistence->allowsHistory($module, $request->user())) {
            $run = $recorder->start(
                module: $module,
                ruleVersion: new RuleVersion('2026.1'),
                referenceDate: ReferenceDate::fromString($input['competence'].'-01'),
                input: $input,
                userId: (int) $request->user()->getAuthIdentifier(),
            );
            $recorder->succeed($run, $result->toArray());
            $saved = true;
        }

        return view('tools-calculadora-pro-labore-distribuicao-lucros::index', [
            ...$page->execute(),
            'result' => $result,
            'historySaved' => $saved,
            'recentHistory' => $request->user() === null ? [] : app(ManageToolHistory::class)->recent((int) $request->user()->getAuthIdentifier()),
        ]);
    }

    public function simulate(SimulateScenariosRequest $request, SimulateScenarios $action, ShowToolPage $page): View
    {
        return view('tools-calculadora-pro-labore-distribuicao-lucros::index', [
            ...$page->execute(),
            'simulationResult' => $action->execute($request->validated()),
            'recentHistory' => [],
        ]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, string $format, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    {
        $input = $request->validated();
        $result = $action->execute($input)->toArray();

        return $this->export($format, $result, $input, $print, $tabular);
    }

    public function history(Request $request, ManageToolHistory $history): View
    {
        return view('tools-calculadora-pro-labore-distribuicao-lucros::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), page: max(1, $request->integer('page', 1))),
        ]);
    }

    public function showHistory(Request $request, string $run, ManageToolHistory $history): View
    {
        return view('tools-calculadora-pro-labore-distribuicao-lucros::history.show', [
            'run' => $history->owned($run, (int) $request->user()->getAuthIdentifier()),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-pro-labore-distribuicao-lucros.index')
            ->withInput($entry->input)
            ->with('history_message', 'Os dados salvos foram carregados. Revise as premissas antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-pro-labore-distribuicao-lucros.history.index')
            ->with('history_message', 'Simulação removida do histórico.');
    }

    public function exportHistory(Request $request, string $run, string $format, ManageToolHistory $history, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return $this->export($format, $entry->result, $entry->input, $print, $tabular);
    }

    /** @param array<string,mixed> $result @param array<string,mixed> $input */
    private function export(string $format, array $result, array $input, BrowserPrintExporter $print, TabularExportService $tabular): View|JsonResponse|StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'json', 'pdf'], true), 404);
        $filename = 'pro-labore-lucros-'.($input['competence'] ?? now()->format('Y-m'));

        if ($format === 'json') {
            return response()->json(['input' => $input, 'result' => $result], 200, [
                'Content-Disposition' => 'attachment; filename="'.$filename.'.json"',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        if ($format === 'csv') {
            $rows = [];
            foreach (($result['summary'] ?? []) as $item) {
                $rows[] = [$item['label'] ?? '', $item['value'] ?? '', $item['description'] ?? ''];
            }

            return $tabular->csv($filename.'.csv', ['Indicador', 'Valor', 'Descrição'], $rows);
        }

        return $print->render(new PrintableDocument(
            title: 'Relatório de Pró-Labore e Distribuição de Lucros',
            subtitle: 'Competência '.($input['competence'] ?? 'não informada'),
            contentView: 'tools-calculadora-pro-labore-distribuicao-lucros::pdf.report',
            data: ['input' => $input, 'result' => $result],
            generatedAt: now()->format('d/m/Y H:i'),
            summaryLabel: $result['summary'][2]['label'] ?? 'Total recebido',
            summaryValue: $result['summary'][2]['value'] ?? '—',
        ));
    }
}
