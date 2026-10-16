<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Presentation\Controllers;

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
use App\Tools\OvertimeCalculator\Application\Data\CalculationInput;
use App\Tools\OvertimeCalculator\Domain\Services\Calculator;
use App\Tools\OvertimeCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\OvertimeCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(Tool $module): View
    {
        return view('tools-overtime-pay-calculator::index', ['tool' => $module->manifest()]);
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);
        try {
            $result = $calculator->calculate($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'overtime-pay-calculator.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-overtime-pay-calculator::index', ['tool' => $module->manifest(), 'result' => $result, 'historySaved' => $run !== null]);
    }

    public function exportCurrent(ExecuteToolRequest $request, Calculator $calculator, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $calculator->calculate($input);
        $filename = 'overtime-pay-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Overtime Pay Report', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        return new CalculationInput(
            competence: $d['competence'],
            hourlyRate: $d['hourly_rate'],
            regularHours: $d['regular_hours'],
            overtimeHours: $d['overtime_hours'],
            overtimeMultiplier: (string) ($d['overtime_multiplier'] ?? '1.5'),
            nondiscretionaryBonus: $d['nondiscretionary_bonus'] ?? '0',
        );
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString($input->competence.'-01'), $input->toArray(), $request->user()->id);
    }
}
