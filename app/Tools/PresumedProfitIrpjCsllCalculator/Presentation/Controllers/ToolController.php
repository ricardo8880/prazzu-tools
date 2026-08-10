<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions\CalculateTool;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Actions\ShowToolPage;
use App\Tools\PresumedProfitIrpjCsllCalculator\Application\Data\CalculationInput;
use App\Tools\PresumedProfitIrpjCsllCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\PresumedProfitIrpjCsllCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-calculadora-irpj-csll-lucro-presumido::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, ShowToolPage $page): View
    {
        $input = $this->input($request->validated());
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);
        try {
            $result = $action->execute($input);
            if ($run !== null) $recorder->succeed($run, $result->toPersistenceArray());
        } catch (Throwable $e) {
            if ($run !== null) $recorder->fail($run, 'calculadora-irpj-csll-lucro-presumido.calculation_failed');
            throw $e;
        }
        $request->flash();

        return view('tools-calculadora-irpj-csll-lucro-presumido::index', [
            ...$page->execute(), 'result' => $result, 'historySaved' => $run !== null, 'calculationInput' => $request->validated(),
        ]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'irpj-csll-lucro-presumido-'.now()->format('Y-m-d');

        return $format === 'pdf'
            ? $pdf->download($documents->pdf('IRPJ e CSLL — Lucro Presumido', $filename, $result, $input->toArray()))
            : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        return new CalculationInput(
            (int) $d['quarter'], (string) $d['commerce_revenue'], (string) $d['fuel_revenue'],
            (string) $d['passenger_transport_revenue'], (string) $d['services_revenue'],
            (string) $d['other_taxable_additions'], (string) $d['prior_irpj_presumption_revenue'],
            (string) $d['prior_csll_presumption_revenue'], (string) $d['irpj_credits'], (string) $d['csll_credits'],
        );
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $persistence->allowsHistory($module, $request->user())) return null;
        $month = str_pad((string) ($input->quarter * 3), 2, '0', STR_PAD_LEFT);
        return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString('2026-'.$month.'-01'), $input->toArray(), $request->user()->id);
    }
}
