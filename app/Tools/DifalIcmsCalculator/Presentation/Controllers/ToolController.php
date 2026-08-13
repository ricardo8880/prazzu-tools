<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\DifalIcmsCalculator\Application\Data\CalculationInput;
use App\Tools\DifalIcmsCalculator\Domain\Services\Calculator;
use App\Tools\DifalIcmsCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\DifalIcmsCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-difal-icms::index');
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $features->requireIf(! isset($data['interstate_rate']) || (string) $data['interstate_rate'] === '', $module, 'interstate_assist', $request);
        $features->requireIf(($data['method'] ?? 'single_base') === 'double_base', $module, 'double_base', $request);
        $features->requireIf((float) ($data['fcp_rate'] ?? 0) > 0, $module, 'fcp', $request);
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);
        try {
            $result = $calculator->calculate($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'calculadora-difal-icms.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-calculadora-difal-icms::index', ['result' => $result, 'historySaved' => $run !== null, 'calculationInput' => $request->validated()]);
    }

    public function exportCurrent(ExecuteToolRequest $request, Calculator $calculator, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $calculator->calculate($input);
        $filename = 'difal-icms-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Relatório de DIFAL / ICMS / FCP', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        return new CalculationInput($d['competence'], $d['base'], $d['origin_uf'], $d['destination_uf'], (bool) ($d['imported'] ?? false), isset($d['interstate_rate']) && (string) $d['interstate_rate'] !== '' ? (string) $d['interstate_rate'] : null, (string) $d['internal_rate'], (string) ($d['fcp_rate'] ?? '0'), (string) $d['method'], (bool) ($d['recipient_taxpayer'] ?? false));
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString($input->competence.'-01'), $input->toArray(), $request->user()->id);
    }
}
