<?php

declare(strict_types=1);
namespace App\Tools\TaxReformSimulator\Presentation\Controllers;
use App\Http\Controllers\Controller;
use App\Tools\TaxReformSimulator\Application\Actions\CalculateTool;
use App\Tools\TaxReformSimulator\Application\Actions\ShowToolPage;
use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use App\Tools\TaxReformSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page): View { return view('tools-sales-tax-calculator::index',$page->execute()); }
    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ShowToolPage $page): View
    {
        $d=$request->validated();
        $r=$action->execute(new CalculationInput((string)$d['price'],(string)$d['state_rate'],(string)($d['local_rate']??'0'),(string)($d['district_rate']??'0'),(int)$d['quantity'],(bool)($d['tax_included']??false)));
        $request->flash();
        return view('tools-sales-tax-calculator::index',[...$page->execute(),'result'=>$r]);
    }
}
