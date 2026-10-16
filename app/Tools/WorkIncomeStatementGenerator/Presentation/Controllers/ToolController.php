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
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    private const PLUS_SPREADSHEET_FEATURE = 'spreadsheet_export';

    public function index(ShowToolPage $page): View
    {
        return view('tools-employment-income-letter-generator::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        $input = $request->validated();
        $request->session()->flashInput($input);

        return view('tools-employment-income-letter-generator::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($input),
        ]);
    }

    public function downloadPdf(ExecuteToolRequest $request, CalculateTool $action, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        $result = $action->execute($input);

        return $exporter->download($documents->pdf('Employment and Income Verification Letter', 'employment-income-letter-generator-'.now()->format('Y-m-d'), $result, $input));
    }

    public function downloadExcel(ExecuteToolRequest $request, CalculateTool $action, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        $result = $action->execute($input);

        return $exporter->download($documents->spreadsheet('employment-income-letter-generator-'.now()->format('Y-m-d'), $result, $input));
    }
}
