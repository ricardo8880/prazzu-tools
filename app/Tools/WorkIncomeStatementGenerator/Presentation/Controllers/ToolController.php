<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\WorkIncomeStatementGenerator\Application\Actions\CalculateTool;
use App\Tools\WorkIncomeStatementGenerator\Application\Actions\ShowToolPage;
use App\Tools\WorkIncomeStatementGenerator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-declaracao-trabalho-renda::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();
        $request->session()->flashInput($input);

        return view('tools-declaracao-trabalho-renda::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
        ]);
    }

    public function downloadPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): \Symfony\Component\HttpFoundation\Response
    { $input=$request->validated(); $result=$action->execute($input); return $exporter->download($documents->pdf('Declaração de Trabalho/Renda', 'declaracao-trabalho-renda-'.now()->format('Y-m-d'), $result, $input)); }
    public function downloadExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): \Symfony\Component\HttpFoundation\Response
    { $input=$request->validated(); $result=$action->execute($input); return $exporter->download($documents->spreadsheet('declaracao-trabalho-renda-'.now()->format('Y-m-d'), $result, $input)); }
}
