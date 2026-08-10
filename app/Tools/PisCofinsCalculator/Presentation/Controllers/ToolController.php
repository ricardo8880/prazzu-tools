<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\PisCofinsCalculator\Application\Actions\CalculateTool;
use App\Tools\PisCofinsCalculator\Application\Actions\ShowToolPage;
use App\Tools\PisCofinsCalculator\Application\Data\CalculationInput;
use App\Tools\PisCofinsCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\PisCofinsCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View { return view('tools-calculadora-pis-cofins::index',$page->execute()); }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, ShowToolPage $page): View
    {
        $input = $this->input($request->validated());
        $run = $this->startRun($request,$recorder,$persistence,$module,$input);
        try {
            $result = $action->execute($input);
            if ($run !== null) $recorder->succeed($run,$result->toPersistenceArray());
        } catch (Throwable $e) {
            if ($run !== null) $recorder->fail($run,'calculadora-pis-cofins.calculation_failed');
            throw $e;
        }
        $request->flash();
        return view('tools-calculadora-pis-cofins::index',[...$page->execute(),'result'=>$result,'historySaved'=>$run!==null,'calculationInput'=>$request->validated()]);
    }

    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format,['pdf','xlsx'],true),404);
        $input = $this->input($request->validated());
        $result = $action->execute($input);
        $filename = 'pis-cofins-'.$input->period;
        return $format === 'pdf'
            ? $pdf->download($documents->pdf('Calculadora PIS e COFINS',$filename,$result,$input->toArray()))
            : $spreadsheet->download($documents->spreadsheet($filename,$result,$input->toArray()));
    }

    private function input(array $d): CalculationInput
    {
        $operations = [];
        foreach (($d['operations'] ?? []) as $op) {
            $operations[] = ['description'=>(string)($op['description'] ?? ''),'revenue'=>(string)($op['revenue'] ?? '0'),'credit_base'=>(string)($op['credit_base'] ?? '0')];
        }
        return new CalculationInput((string)$d['period'],(string)$d['regime'],(bool)($d['compare_regimes'] ?? false),(string)$d['taxable_revenue'],(string)$d['credit_base'],(string)$d['pis_withheld'],(string)$d['cofins_withheld'],$operations);
    }

    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if (! $request->user() || ! $persistence->allowsHistory($module,$request->user())) return null;
        return $recorder->start($module,new RuleVersion('2026.1.0'),ReferenceDate::fromString($input->period.'-01'),$input->toArray(),$request->user()->id);
    }
}
