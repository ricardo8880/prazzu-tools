<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\TurnoverCalculator\Application\Actions\CalculateTool;
use App\Tools\TurnoverCalculator\Application\Actions\ShowToolPage;
use App\Tools\TurnoverCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-calculadora-turnover::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ShowToolPage $page): View
    {
        return view('tools-calculadora-turnover::index', [
            ...$page->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
