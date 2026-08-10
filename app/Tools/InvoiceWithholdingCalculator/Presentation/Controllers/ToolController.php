<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\InvoiceWithholdingCalculator\Application\Actions\CalculateTool;
use App\Tools\InvoiceWithholdingCalculator\Application\Actions\ShowToolPage;
use App\Tools\InvoiceWithholdingCalculator\Application\Data\CalculationInput;
use App\Tools\InvoiceWithholdingCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\InvoiceWithholdingCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View { return view('tools-calculadora-retencoes-nota-fiscal::index',$page->execute()); }
    public function calculate(ExecuteToolRequest $request,CalculateTool $action,ToolRunRecorder $recorder,ToolPersistenceAuthorizer $persistence,Tool $module,ShowToolPage $page): View
    {
        $input=$this->input($request->validated()); $run=$this->startRun($request,$recorder,$persistence,$module,$input);
        try{$result=$action->execute($input); if($run!==null)$recorder->succeed($run,$result->toPersistenceArray());}
        catch(Throwable $e){if($run!==null)$recorder->fail($run,'calculadora-retencoes-nota-fiscal.calculation_failed'); throw $e;}
        $request->flash(); return view('tools-calculadora-retencoes-nota-fiscal::index',[...$page->execute(),'result'=>$result,'historySaved'=>$run!==null,'calculationInput'=>$request->validated()]);
    }
    public function exportCurrent(ExecuteToolRequest $request,CalculateTool $action,ToolResultExportFactory $documents,PdfExporter $pdf,SpreadsheetExporter $spreadsheet,string $format): Response
    {
        abort_unless(in_array($format,['pdf','xlsx'],true),404); $input=$this->input($request->validated()); $result=$action->execute($input); $filename='retencoes-nota-'.$input->competence;
        return $format==='pdf' ? $pdf->download($documents->pdf('Calculadora de Retenções na Nota Fiscal',$filename,$result,$input->toArray())) : $spreadsheet->download($documents->spreadsheet($filename,$result,$input->toArray()));
    }
    private function input(array $d): CalculationInput
    {
        $notes=[]; foreach(($d['notes']??[]) as $note)$notes[]=['description'=>(string)($note['description']??''),'value'=>(string)($note['value']??'0')];
        return new CalculationInput(
            (string)$d['competence'],(string)($d['invoice_number']??''),(string)$d['service_description'],(string)$d['gross_value'],
            (bool)($d['apply_irrf']??false),(string)$d['irrf_rate'],(string)$d['irrf_base_percent'],
            (bool)($d['apply_inss']??false),(string)$d['inss_rate'],(string)$d['inss_base_percent'],
            (bool)($d['apply_iss']??false),(string)$d['iss_rate'],(string)$d['iss_base_percent'],
            (bool)($d['apply_pis']??false),(string)$d['pis_rate'],(string)$d['pis_base_percent'],
            (bool)($d['apply_cofins']??false),(string)$d['cofins_rate'],(string)$d['cofins_base_percent'],
            (bool)($d['apply_csll']??false),(string)$d['csll_rate'],(string)$d['csll_base_percent'],$notes,
        );
    }
    private function startRun(Request $request,ToolRunRecorder $recorder,ToolPersistenceAuthorizer $persistence,Tool $module,CalculationInput $input): ?ToolRunHandle
    {
        if(!$request->user()||!$persistence->allowsHistory($module,$request->user())) return null;
        return $recorder->start($module,new RuleVersion('2026.1.0'),ReferenceDate::fromString($input->competence.'-01'),$input->toArray(),$request->user()->id);
    }
}
