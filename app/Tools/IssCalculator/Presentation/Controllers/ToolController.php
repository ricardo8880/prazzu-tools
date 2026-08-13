<?php

declare(strict_types=1);

namespace App\Tools\IssCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Money\Money;
use App\Http\Controllers\Controller;
use App\Tools\IssCalculator\Application\Actions\CalculateTool;
use App\Tools\IssCalculator\Application\Actions\ShowToolPage;
use App\Tools\IssCalculator\Application\Data\CalculationInput;
use App\Tools\IssCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\IssCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        return view('tools-calculadora-iss::index', [...$page->execute(), 'plusEnabled' => $features->plusEnabled($module, $request), 'monthlyConsolidationAllowed' => $features->allows($module, 'monthly_consolidation', $request)]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page): View
    {
        $data = $request->validated();
        $features->requireIf((bool) ($data['retained'] ?? false), $module, 'retention', $request);
        $hasServices = false;
        $hasRetainedAdditional = false;
        foreach (($data['services'] ?? []) as $service) {
            $hasServices = $hasServices || $this->moneyPositive($service['value'] ?? '0');
            $hasRetainedAdditional = $hasRetainedAdditional || (bool) ($service['retained'] ?? false);
        }
        $features->requireIf($hasServices, $module, 'multiple_services', $request);
        $features->requireIf($hasRetainedAdditional, $module, 'retention', $request);
        $features->requireIf(count($data['municipality_scenarios'] ?? []) > 0, $module, 'municipality_scenarios', $request);

        $input = $this->input($data);
        $result = $action->execute($input);
        $request->flash();

        return view('tools-calculadora-iss::index', [...$page->execute(), 'result' => $result, 'calculationInput' => $data, 'plusEnabled' => $features->plusEnabled($module, $request), 'monthlyConsolidationAllowed' => $features->allows($module, 'monthly_consolidation', $request)]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'calculadora-iss-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Calculadora de ISS', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        $services = [['competence' => $d['competence'], 'municipality' => $d['municipality'], 'service' => $d['service'], 'taker' => $d['taker'] ?? '', 'value' => $d['value'], 'rate' => $d['rate'], 'retained' => (bool) ($d['retained'] ?? false)]];
        foreach (($d['services'] ?? []) as $x) {
            if (trim((string) ($x['value'] ?? '')) === '') {
                continue;
            }
            $services[] = ['competence' => $x['competence'] ?? $d['competence'], 'municipality' => $x['municipality'] ?? $d['municipality'], 'service' => $x['service'] ?? 'Serviço adicional', 'taker' => $x['taker'] ?? '', 'value' => $x['value'], 'rate' => $x['rate'] ?? $d['rate'], 'retained' => (bool) ($x['retained'] ?? false)];
        }
        $scenarios = [];
        foreach (($d['municipality_scenarios'] ?? []) as $x) {
            if (trim((string) ($x['municipality'] ?? '')) === '' || trim((string) ($x['rate'] ?? '')) === '') {
                continue;
            }
            $scenarios[] = ['municipality' => $x['municipality'], 'rate' => $x['rate']];
        }

        return new CalculationInput($services, $scenarios);
    }

    private function moneyPositive(mixed $value): bool
    {
        return Money::fromDecimal((string) ($value ?: '0'))->minorAmount() > 0;
    }
}
