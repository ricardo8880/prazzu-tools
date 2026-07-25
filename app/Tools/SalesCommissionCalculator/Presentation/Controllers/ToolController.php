<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\SalesCommissionCalculator\Application\Actions\CalculateTool;
use App\Tools\SalesCommissionCalculator\Application\Actions\ShowToolPage;
use App\Tools\SalesCommissionCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-comissao-vendedores::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-comissao-vendedores::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
