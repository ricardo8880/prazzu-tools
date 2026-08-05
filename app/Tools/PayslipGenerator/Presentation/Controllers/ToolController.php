<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\PayslipGenerator\Application\Actions\CalculateTool;
use App\Tools\PayslipGenerator\Application\Actions\ShowToolPage;
use App\Tools\PayslipGenerator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-gerador-holerite::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();
        return view('tools-gerador-holerite::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
            'calculationInput' => $input,
        ]);
    }

    public function exportPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->pdf('Holerite', 'holerite-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->spreadsheet('holerite-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }
}
