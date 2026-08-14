<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Http\Controllers\Controller;
use App\Tools\ActualProfitCalculator\{Tool};
use App\Tools\ActualProfitCalculator\Application\Actions\{CalculateTool,ShowToolPage};
use App\Tools\ActualProfitCalculator\Application\Data\CalculationInput;
use App\Tools\ActualProfitCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View; use Illuminate\Http\Request;
final class ToolController extends Controller { public function index(Request $request,ShowToolPage $page):View{return view('tools-calculadora-lucro-real::index',$page->execute());} public function calculate(ExecuteToolRequest $request,CalculateTool $action,ShowToolPage $page,ToolFeatureRequestAuthorizer $features,Tool $module):View{$d=$request->validated();$diag=(bool)($d['show_diagnostics']??false);$features->requireIf($diag,$module,'tax_base_diagnostics',$request);$r=$action->execute(new CalculationInput($d['accounting_profit'],$d['additions']??'0',$d['exclusions']??'0',$d['irpj_loss_balance']??'0',$d['csll_negative_balance']??'0',(int)$d['months']));$request->flash();return view('tools-calculadora-lucro-real::index',[...$page->execute(),'result'=>$r,'showDiagnostics'=>$diag]);} }
