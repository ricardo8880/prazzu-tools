<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\LaborChargesCalculator\Application\Actions\CalculateTool;
use App\Tools\LaborChargesCalculator\Application\Actions\ShowToolPage;
use App\Tools\LaborChargesCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-encargos-trabalhistas::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-encargos-trabalhistas::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
