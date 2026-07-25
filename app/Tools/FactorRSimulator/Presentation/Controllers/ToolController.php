<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\FactorRSimulator\Application\Actions\CalculateTool;
use App\Tools\FactorRSimulator\Application\Actions\ShowToolPage;
use App\Tools\FactorRSimulator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-simulador-fator-r::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-simulador-fator-r::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
