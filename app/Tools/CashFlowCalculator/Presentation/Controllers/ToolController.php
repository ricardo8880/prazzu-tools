<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\CashFlowCalculator\Application\Actions\CalculateTool;
use App\Tools\CashFlowCalculator\Application\Actions\ShowToolPage;
use App\Tools\CashFlowCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-fluxo-de-caixa::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-fluxo-de-caixa::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
