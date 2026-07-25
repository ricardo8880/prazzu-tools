<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\LateDasCalculator\Application\Actions\CalculateTool;
use App\Tools\LateDasCalculator\Application\Actions\ShowToolPage;
use App\Tools\LateDasCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-das-em-atraso::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-das-em-atraso::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
