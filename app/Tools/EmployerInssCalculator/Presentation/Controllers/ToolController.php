<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\EmployerInssCalculator\Application\Actions\CalculateTool;
use App\Tools\EmployerInssCalculator\Application\Actions\ShowToolPage;
use App\Tools\EmployerInssCalculator\Presentation\Requests\ExecuteToolRequest;
use Illuminate\Contracts\View\View;

final class ToolController extends Controller
{
    public function index(ShowToolPage $page): View
    {
        return view('tools-inss-patronal::index', $page->execute());
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action): View
    {
        return view('tools-inss-patronal::index', [
            ...app(ShowToolPage::class)->execute(),
            'result' => $action->execute($request->validated()),
        ]);
    }
}
