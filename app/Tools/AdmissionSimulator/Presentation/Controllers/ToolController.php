<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\AdmissionSimulator\Application\Actions\CalculateTool;
use App\Tools\AdmissionSimulator\Application\Actions\ShowToolPage;
use App\Tools\AdmissionSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-simulador-admissao::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();
        return view('tools-simulador-admissao::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
            'calculationInput' => $input,
        ]);
    }

    public function exportPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->pdf('Simulação de Admissão', 'simulacao-admissao-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->spreadsheet('simulacao-admissao-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }
}
