<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Http\Controllers\Controller;
use App\Tools\RetroactiveDasRegularizationCalculator\Application\Actions\CalculateTool;
use App\Tools\RetroactiveDasRegularizationCalculator\Application\Actions\ShowToolPage;
use App\Tools\RetroactiveDasRegularizationCalculator\Application\Data\CalculationInput;
use App\Tools\RetroactiveDasRegularizationCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\RetroactiveDasRegularizationCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-calculadora-das-retroativo-regularizacao-simples::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $plusEnabled = $features->plusEnabled($module, $request);
        $features->requireIf(count($data['competencies'] ?? []) > 0, $module, 'multiple_competencies', $request);
        if (! $plusEnabled) {
            $data['competencies'] = [];
            $data['regularization_months'] = 1;
        }
        $input = $this->input($data);
        $result = $action->execute($input);
        $request->flash();
        return view('tools-calculadora-das-retroativo-regularizacao-simples::index', [...$page->execute(), 'result' => $result, 'calculationInput' => $data, 'plusEnabled' => $plusEnabled]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $name = 'das-retroativo-regularizacao-simples-'.now()->format('Y-m-d');
        return $format === 'pdf' ? $pdf->download($documents->pdf('DAS Retroativo + Regularização do Simples', $name, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($name, $result, $input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        $competencies = [['competence' => $d['competence'], 'revenue' => $d['revenue'], 'effective_rate' => $d['effective_rate'], 'due_date' => $d['due_date'], 'update_date' => $d['update_date'], 'accumulated_selic' => $d['accumulated_selic']]];
        foreach (($d['competencies'] ?? []) as $c) {
            if (trim((string) ($c['revenue'] ?? '')) === '') continue;
            $competencies[] = ['competence' => $c['competence'] ?? $d['competence'], 'revenue' => $c['revenue'], 'effective_rate' => $c['effective_rate'] ?? $d['effective_rate'], 'due_date' => $c['due_date'] ?? $d['due_date'], 'update_date' => $c['update_date'] ?? $d['update_date'], 'accumulated_selic' => $c['accumulated_selic'] ?? $d['accumulated_selic']];
        }
        return new CalculationInput($competencies, (int) ($d['regularization_months'] ?? 1));
    }
}
