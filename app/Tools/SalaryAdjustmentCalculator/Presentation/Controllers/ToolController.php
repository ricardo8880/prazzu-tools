<?php

declare(strict_types=1);

namespace App\Tools\SalaryAdjustmentCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\SalaryAdjustmentCalculator\Application\Actions\CalculateTool;
use App\Tools\SalaryAdjustmentCalculator\Application\Actions\ShowToolPage;
use App\Tools\SalaryAdjustmentCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-reajuste-salarial::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-reajuste-salarial::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
