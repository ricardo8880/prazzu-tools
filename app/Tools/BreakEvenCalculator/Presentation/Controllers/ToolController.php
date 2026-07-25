<?php

declare(strict_types=1);

namespace App\Tools\BreakEvenCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\BreakEvenCalculator\Application\Actions\CalculateTool;
use App\Tools\BreakEvenCalculator\Application\Actions\ShowToolPage;
use App\Tools\BreakEvenCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-ponto-de-equilibrio::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-ponto-de-equilibrio::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
