<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\SalesCommissionCalculator\Application\Actions\CalculateSellerBatch;
use App\Tools\SalesCommissionCalculator\Application\Actions\CalculateTool;
use App\Tools\SalesCommissionCalculator\Application\Actions\ShowToolPage;
use App\Tools\SalesCommissionCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-comissao-vendedores::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();

        return view('tools-comissao-vendedores::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
            'calculationInput' => $input,
        ]);
    }

    public function calculateBatch(Request $request, CalculateSellerBatch $action, ShowToolPage $page): View
    {
        $data = $request->validate([
            'seller_batch' => ['required', 'string', 'max:10000'], 'rate' => ['required', 'numeric', 'between:0,100'],
            'goal' => ['required'], 'goal_bonus_rate' => ['required', 'numeric', 'between:0,100'],
        ]);

        return view('tools-comissao-vendedores::index', [...$page->execute(), 'sellerBatchResults' => $action->execute($data)]);
    }

    public function exportPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->pdf('Calculadora de Comissão de Vendedores', 'comissao-vendedores-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->spreadsheet('comissao-vendedores-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }
}
