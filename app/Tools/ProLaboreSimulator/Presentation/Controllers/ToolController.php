<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Presentation\Controllers;

use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\ProLaboreSimulator\Application\Data\CalculationInput;
use App\Tools\ProLaboreSimulator\Domain\Services\Calculator;
use App\Tools\ProLaboreSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(): View { return view('tools-simulador-pro-labore-ideal::index'); }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator): View
    {
        $input = $request->validated();
        return view('tools-simulador-pro-labore-ideal::index', ['result' => $this->result($calculator, $input), 'calculationInput' => $input]);
    }

    public function exportPdf(ExecuteToolRequest $request, Calculator $calculator, PdfExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->pdf('Simulação de Pró-Labore Ideal', 'pro-labore-'.now()->format('Y-m-d'), $this->result($calculator, $input), $input));
    }

    public function exportExcel(ExecuteToolRequest $request, Calculator $calculator, SpreadsheetExporter $exporter, ToolResultExportFactory $documents): Response
    {
        $input = $request->validated();
        return $exporter->download($documents->spreadsheet('pro-labore-'.now()->format('Y-m-d'), $this->result($calculator, $input), $input));
    }

    private function result(Calculator $calculator, array $data): \App\Core\Tools\Calculation\Data\ToolCalculationResult
    {
        return $calculator->calculate(new CalculationInput(
            competence: $data['competence'], companyRegime: $data['company_regime'], grossProLabore: $data['gross_pro_labore'],
            dependents: (int) ($data['dependents'] ?? 0), otherOfficialSocialSecurity: $data['other_official_social_security'] ?? '0',
        ));
    }
}
