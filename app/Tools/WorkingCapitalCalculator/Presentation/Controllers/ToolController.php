<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\WorkingCapitalCalculator\Application\Actions\CalculateTool;
use App\Tools\WorkingCapitalCalculator\Application\Actions\ShowToolPage;
use App\Tools\WorkingCapitalCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-capital-de-giro::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-capital-de-giro::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
            'calculationInput' => $request->validated(),
        ]);
    }


    public function exportPdf(
        ExecuteToolRequest $request,
        CalculateTool $action,
        PdfExporter $exporter,
        ToolResultExportFactory $documents,
    ): Response {
        $input = $request->validated();
        $result = $action->execute($input);

        return $exporter->download($documents->pdf(
            title: 'Cálculo de Capital de Giro',
            filename: 'capital-de-giro-'.now()->format('Y-m-d'),
            result: $result,
            input: $input,
        ));
    }

    public function exportExcel(
        ExecuteToolRequest $request,
        CalculateTool $action,
        SpreadsheetExporter $exporter,
        ToolResultExportFactory $documents,
    ): Response {
        $input = $request->validated();
        $result = $action->execute($input);

        return $exporter->download($documents->spreadsheet(
            filename: 'capital-de-giro-'.now()->format('Y-m-d'),
            result: $result,
            input: $input,
        ));
    }
}
