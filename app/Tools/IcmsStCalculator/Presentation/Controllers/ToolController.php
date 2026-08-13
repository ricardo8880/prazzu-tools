<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Money\Money;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\IcmsStCalculator\Application\Actions\CalculateTool;
use App\Tools\IcmsStCalculator\Application\Actions\ManageToolHistory;
use App\Tools\IcmsStCalculator\Application\Actions\ShowToolPage;
use App\Tools\IcmsStCalculator\Application\Data\CalculationInput;
use App\Tools\IcmsStCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\IcmsStCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-calculadora-icms-st::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $features->requireIf(($data['operation_type'] ?? 'internal') === 'interstate', $module, 'interstate', $request);
        $features->requireIf((bool) ($data['adjust_mva'] ?? false), $module, 'adjusted_mva', $request);
        $features->requireIf((float) ($data['fcp_rate'] ?? 0) > 0, $module, 'fcp', $request);
        $hasItems = false;
        foreach (($data['items'] ?? []) as $item) {
            $hasItems = $hasItems || $this->moneyPositive($item['merchandise_value'] ?? '0');
        }
        $features->requireIf($hasItems, $module, 'multiple_items', $request);

        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $features, $module, $input);
        try {
            $result = $action->execute($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'calculadora-icms-st.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-calculadora-icms-st::index', [...$page->execute(), 'result' => $result, 'historySaved' => $run !== null, 'calculationInput' => $data, 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'icms-st-'.$input->competence;

        return $format === 'pdf' ? $pdf->download($documents->pdf('Calculadora de ICMS-ST', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    public function history(Request $request, ManageToolHistory $history): View
    {
        return view('tools-calculadora-icms-st::history.index', [
            'runs' => $history->paginate((int) $request->user()->getAuthIdentifier(), max(1, $request->integer('page', 1))),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.calculadora-icms-st.index')->withInput([...$entry->input, 'confirm_scope' => 1])
            ->with('history_message', 'Dados recuperados. Revise as premissas antes de calcular novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageToolHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return back()->with('history_message', 'Cálculo removido do histórico.');
    }

    private function input(array $d): CalculationInput
    {
        $items = [];
        foreach (($d['items'] ?? []) as $item) {
            $items[] = ['description' => (string) ($item['description'] ?? ''), 'merchandise_value' => (string) ($item['merchandise_value'] ?? '0'), 'mva' => (string) ($item['mva'] ?? '')];
        }

        return new CalculationInput((string) $d['competence'], (string) $d['operation_type'], (string) $d['origin_uf'], (string) $d['destination_uf'], (string) $d['merchandise_value'], (string) $d['freight'], (string) $d['insurance'], (string) $d['other_charges'], (string) $d['ipi'], (string) $d['discount'], (string) $d['original_mva'], (string) $d['internal_rate'], (string) ($d['interstate_rate'] ?? ''), (bool) ($d['adjust_mva'] ?? false), (string) ($d['fcp_rate'] ?? '0'), (string) ($d['own_icms_override'] ?? ''), $items);
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $features->allows($module, 'history', $request) || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

        return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString($input->competence.'-01'), $input->toArray(), $request->user()->id);
    }

    private function moneyPositive(mixed $value): bool
    {
        return Money::fromDecimal((string) ($value ?: '0'))->minorAmount() > 0;
    }
}
