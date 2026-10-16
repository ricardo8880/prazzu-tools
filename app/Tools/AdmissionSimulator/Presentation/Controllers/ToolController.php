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
    private const PLUS_SPREADSHEET_FEATURE = 'spreadsheet_export';

    public function index(ShowToolPage $page): View
    {
        return view('tools-new-hire-cost-calculator::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();

        return view('tools-new-hire-cost-calculator::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
            'calculationInput' => $input,
        ]);
    }

    public function exportPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->pdf('New Hire Cost Estimate', 'new-hire-cost-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();

        return $exporter->download($documents->spreadsheet('new-hire-cost-'.now()->format('Y-m-d'), $action->execute($input), $input));
    }
}
