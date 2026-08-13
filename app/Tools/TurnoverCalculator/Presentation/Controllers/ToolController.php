<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\TurnoverCalculator\Application\Actions\AnalyzeSegments;
use App\Tools\TurnoverCalculator\Application\Actions\CalculateTool;
use App\Tools\TurnoverCalculator\Application\Actions\ShowToolPage;
use App\Tools\TurnoverCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function analyzeSegments(Request $request, AnalyzeSegments $action, ShowToolPage $page): View
    {
        $data = $request->validate(['segments' => ['required', 'string', 'max:2000']]);

        return view('tools-calculadora-turnover::index', [
            ...$page->execute(),
            'segmentAnalysis' => $action->execute($data['segments']),
            'segmentInput' => $data['segments'],
        ]);
    }
}
