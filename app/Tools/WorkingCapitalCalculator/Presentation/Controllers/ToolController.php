<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\WorkingCapitalCalculator\Application\Actions\CalculateTool;
use App\Tools\WorkingCapitalCalculator\Application\Actions\ShowToolPage;
use App\Tools\WorkingCapitalCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-capital-de-giro::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-capital-de-giro::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
