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
    public function index(): View
    {
        return view('tools-calculadora-hora-extra::index');
    }

    public function calculate(ExecuteToolRequest $request, Calculator $calculator, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, ToolFeatureRequestAuthorizer $features, Tool $module): View
    {
        $data = $request->validated();
        $features->requireIf($this->decimalPositive($data['night_clock_hours'] ?? '0') || $this->decimalPositive($data['night_overtime_hours'] ?? '0'), $module, 'night', $request);
        $features->requireIf((bool) ($data['include_dsr'] ?? false), $module, 'dsr', $request);
        $features->requireIf((bool) ($data['include_reflexes'] ?? false), $module, 'reflexes', $request);
        $input = $this->input($data);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);
        try {
            $result = $calculator->calculate($input);
            if ($run !== null) {
                $recorder->succeed($run, $result->toPersistenceArray());
            }
        } catch (Throwable $e) {
            if ($run !== null) {
                $recorder->fail($run, 'calculadora-hora-extra.calculation_failed');
            } throw $e;
        }
        $request->flash();

        return view('tools-calculadora-hora-extra::index', ['result' => $result, 'historySaved' => $run !== null]);
    }

    public function exportCurrent(ExecuteToolRequest $request, Calculator $calculator, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $input = $this->input($request->validated());
        $result = $calculator->calculate($input);
        $filename = 'hora-extra-'.now()->format('Y-m-d');

        return $format === 'pdf' ? $pdf->download($documents->pdf('Relatório de Hora Extra e Adicional Noturno', $filename, $result, $input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function decimalPositive(mixed $value): bool
    {
        return (float) str_replace(',', '.', (string) ($value ?: '0')) > 0;
    }

    private function input(array $d): CalculationInput
    {
        return new CalculationInput($d['competence'], $d['base_salary'], (int) $d['monthly_hours'], $d['overtime_50_hours'] ?? '0', $d['overtime_100_hours'] ?? '0', $d['custom_overtime_hours'] ?? '0', (string) ($d['custom_premium'] ?? '50'), $d['night_clock_hours'] ?? '0', $d['night_overtime_hours'] ?? '0', (string) ($d['night_overtime_premium'] ?? '50'), (int) ($d['working_days'] ?? 0), (int) ($d['rest_days'] ?? 0), (bool) ($d['include_dsr'] ?? false), (bool) ($d['include_reflexes'] ?? false));
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

return $recorder->start($module, new RuleVersion('2026.1.0'), ReferenceDate::fromString($input->competence.'-01'), $input->toArray(), $request->user()->id);
    }
}
