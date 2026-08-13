<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\BreakEvenCalculator\Application\Actions\CalculateTool;
use App\Tools\BreakEvenCalculator\Application\Actions\CompareScenarios;
use App\Tools\BreakEvenCalculator\Application\Actions\ShowToolPage;
use App\Tools\BreakEvenCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-ponto-de-equilibrio::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();

        return view('tools-ponto-de-equilibrio::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
            'calculationInput' => $input,
        ]);
    }

    public function compareScenarios(Request $request, CompareScenarios $action, ShowToolPage $page): View
    {
        $data = $request->validate([
            'fixed_costs' => ['required'], 'sale_price' => ['required'], 'variable_cost' => ['required'], 'scenario_name' => ['nullable', 'string', 'max:60'],
            'fixed_cost_change_rate' => ['required', 'numeric', 'between:-90,500'], 'sale_price_change_rate' => ['required', 'numeric', 'between:-90,500'],
            'variable_cost_change_rate' => ['required', 'numeric', 'between:-90,500'],
        ]);

        return view('tools-ponto-de-equilibrio::index', [...$page->execute(), 'breakEvenScenarios' => $action->execute($data)]);
    }

    public function exportPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->pdf('Calculadora de Ponto de Equilíbrio', 'ponto-de-equilibrio-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->spreadsheet('ponto-de-equilibrio-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }
}
