<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\IcmsStCalculator\Application\Actions\CalculateTool;
use App\Tools\IcmsStCalculator\Application\Actions\ShowToolPage;
use App\Tools\IcmsStCalculator\Application\Data\CalculationInput;
use App\Tools\IcmsStCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\IcmsStCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View { return view('tools-calculadora-icms-st::index',$page->execute()); }
    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, ShowToolPage $page): View
    {
        $input=$this->input($request->validated()); $run=$this->startRun($request,$recorder,$persistence,$module,$input);
        try { $result=$action->execute($input); if($run!==null)$recorder->succeed($run,$result->toPersistenceArray()); }
        catch(Throwable $e){ if($run!==null)$recorder->fail($run,'calculadora-icms-st.calculation_failed'); throw $e; }
        $request->flash();
        return view('tools-calculadora-icms-st::index',[...$page->execute(),'result'=>$result,'historySaved'=>$run!==null,'calculationInput'=>$request->validated()]);
    }
    public function exportCurrent(ExecuteToolRequest $request, CalculateTool $action, ToolResultExportFactory $documents, PdfExporter $pdf, SpreadsheetExporter $spreadsheet, string $format): Response
    {
        abort_unless(in_array($format,['pdf','xlsx'],true),404); $input=$this->input($request->validated()); $result=$action->execute($input); $filename='icms-st-'.$input->competence;
        return $format==='pdf' ? $pdf->download($documents->pdf('Calculadora de ICMS-ST',$filename,$result,$input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename,$result,$input->toArray()));
    }
    private function input(array $d): CalculationInput
    {
        $items=[]; foreach(($d['items']??[]) as $item) $items[]=['description'=>(string)($item['description']??''),'merchandise_value'=>(string)($item['merchandise_value']??'0'),'mva'=>(string)($item['mva']??'')];
        return new CalculationInput((string)$d['competence'],(string)$d['operation_type'],(string)$d['origin_uf'],(string)$d['destination_uf'],(string)$d['merchandise_value'],(string)$d['freight'],(string)$d['insurance'],(string)$d['other_charges'],(string)$d['ipi'],(string)$d['discount'],(string)$d['original_mva'],(string)$d['internal_rate'],(string)($d['interstate_rate']??''),(bool)($d['adjust_mva']??false),(string)($d['fcp_rate']??'0'),(string)($d['own_icms_override']??''),$items);
    }
    private function startRun(Request $request, ToolRunRecorder $recorder, ToolPersistenceAuthorizer $persistence, Tool $module, CalculationInput $input): ?ToolRunHandle
    {
        if(!$request->user()||!$persistence->allowsHistory($module,$request->user())) return null;
        return $recorder->start($module,new RuleVersion('2026.1.0'),ReferenceDate::fromString($input->competence.'-01'),$input->toArray(),$request->user()->id);
    }
}
